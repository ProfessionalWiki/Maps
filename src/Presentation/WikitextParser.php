<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserFactory;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Parser\ParserOutputFlags;
use MediaWiki\Parser\StripState;

class WikitextParser {

	/**
	 * Flags that behavior switches and other page-level settings set
	 */
	private const PAGE_FLAGS = [
		ParserOutputFlags::INDEX_POLICY,
		ParserOutputFlags::NO_INDEX_POLICY,
		ParserOutputFlags::NO_GALLERY,
		ParserOutputFlags::NEW_SECTION,
		ParserOutputFlags::HIDE_NEW_SECTION,
		ParserOutputFlags::SHOW_TOC,
		ParserOutputFlags::NO_TOC,
		ParserOutputFlags::NO_SECTION_EDIT_LINKS,
		ParserOutputFlags::ENABLE_OOUI,
		ParserOutputFlags::PREVENT_CLICKJACKING,
	];

	/**
	 * Markers that the Widgets extension puts in place of widgets, along with the key of each widget's HTML
	 */
	private const WIDGET_MARKER_REGEX = "/\u{FFFC}START_WIDGET(\"?'?-[a-z0-9]{16})END_WIDGET/";

	private const STRIP_MARKER_REGEX = '/' . Parser::MARKER_PREFIX . "[^\x7f<>&'\"]+" . Parser::MARKER_SUFFIX . '/';

	private Parser $pageParser;
	private ParserFactory $parserFactory;
	private ?Parser $textParser = null;
	private ?ParserOptions $textOptions = null;

	/**
	 * @param Parser $pageParser A parser in the middle of a parse, normally the one rendering the page. Each text is
	 * parsed with a copy of its options, and what the parse records is added to the output of the page.
	 */
	public function __construct( Parser $pageParser, ParserFactory $parserFactory ) {
		$this->pageParser = $pageParser;
		$this->parserFactory = $parserFactory;
	}

	public function wikitextToHtml( string $text ): string {
		if ( trim( $text ) === '' ) {
			return '';
		}

		$textParser = $this->startTextParse();
		$html = $this->withWidgets(
			$textParser->recursiveTagParseFully( $this->handOverStripMarkers( $text, $textParser ) ),
			$textParser->getOutput()
		);
		$this->addToPage( $textParser->getOutput() );

		return $html;
	}

	/**
	 * The Widgets extension replaces its markers with the HTML of the widgets when the parse of the page ends, by which
	 * time the markers of the text sit inside the data of the map. So the widgets of the text, and those the page
	 * expanded in the arguments of the map, are filled in here.
	 */
	private function withWidgets( string $html, ParserOutput $textOutput ): string {
		$widgets = (array)$textOutput->getExtensionData( 'widgetReplacements' ) +
			(array)$this->pageParser->getOutput()->getExtensionData( 'widgetReplacements' );

		if ( $widgets === [] ) {
			return $html;
		}

		return preg_replace_callback(
			self::WIDGET_MARKER_REGEX,
			static fn ( array $marker ): string => $widgets[$marker[1]] ?? $marker[0],
			$html
		);
	}

	/**
	 * Each text gets a parse of its own. On the page's parser, core would render every link the page collected so far
	 * again for each text. A copy of the page's parser would share the state that extensions keep on it, which starting
	 * a parse resets.
	 */
	private function startTextParse(): Parser {
		$this->textParser ??= $this->parserFactory->create();
		$this->textOptions ??= clone $this->pageParser->getOptions();

		// The strip markers of the page go along to the parser of the text, whose own strip markers take other names.
		$this->textParser->mMarkerIndex = max( $this->textParser->mMarkerIndex, $this->pageParser->mMarkerIndex );

		$this->textParser->startExternalParse(
			$this->pageParser->getPage(),
			$this->textOptions,
			Parser::OT_HTML,
			true,
			$this->pageParser->getRevisionId()
		);

		return $this->textParser;
	}

	/**
	 * Strip markers in the text belong to the page's parser, so their content goes along to the parser of the text.
	 */
	private function handOverStripMarkers( string $text, Parser $textParser ): string {
		$pageStripState = $this->pageParser->getStripState();

		// When the page's parser is not producing HTML, as when Parsoid has it expand parser functions, extension tags
		// in the text were left unprocessed: their strip markers hold the tags' wikitext, which must not reach the HTML
		// as is.
		if ( $this->pageParser->getOutputType() !== Parser::OT_HTML ) {
			return $pageStripState->unstripBoth( $text );
		}

		$textStripState = $textParser->getStripState();
		$markers = self::stripMarkersIn( $text );
		$handedOver = [];

		while ( $markers !== [] ) {
			$marker = array_pop( $markers );

			if ( !isset( $handedOver[$marker] ) ) {
				$handedOver[$marker] = true;
				array_push( $markers, ...self::stripMarkersIn( $this->handOver( $marker, $pageStripState, $textStripState ) ) );
			}
		}

		return $text;
	}

	/**
	 * @return string[]
	 */
	private static function stripMarkersIn( string $text ): array {
		preg_match_all( self::STRIP_MARKER_REGEX, $text, $markers );

		return $markers[0];
	}

	/**
	 * Each strip marker goes along as its own kind, so that its content reaches the HTML at the same step of the parse
	 * as on the page.
	 *
	 * @return string The content, which can hold strip markers of the other kind
	 */
	private function handOver( string $marker, StripState $pageStripState, StripState $textStripState ): string {
		$noWikiContent = $pageStripState->unstripNoWiki( $marker );

		if ( $noWikiContent !== $marker ) {
			$textStripState->addNoWiki( $marker, $noWikiContent );
			return $noWikiContent;
		}

		$content = $pageStripState->unstripGeneral( $marker );

		if ( $content === $marker ) {
			return $content;
		}

		// Tags that parse wikitext, such as the captions of a gallery, leave placeholders for links that only the page's
		// parser can fill in.
		if ( preg_match( '/<!--(IW)?LINK\'"/', $content ) === 1 ) {
			$content = $this->pageParser->recursiveTagParseFully( $marker );
		}

		$textStripState->addGeneral( $marker, $content );

		return $content;
	}

	private function addToPage( ParserOutput $textOutput ): void {
		self::removePageLevelData( $textOutput );

		$pageOutput = $this->pageParser->getOutput();

		$pageOutput->mergeInternalMetaDataFrom( $textOutput );
		$pageOutput->mergeTrackingMetaDataFrom( $textOutput );
		$pageOutput->mergeHtmlMetaDataFrom( self::htmlMetaDataOf( $textOutput ) );
		$pageOutput->updateCacheExpiry( $textOutput->getCacheExpiry() );
	}

	/**
	 * Page properties and page-level flags, such as the default sort key and those of behavior switches, are for the
	 * wikitext of the page to set. Extensions collect data about the page in extension data, such as Semantic
	 * MediaWiki its semantic data, which the parse of a text collects for the text alone and the merge would put in
	 * place of the page's. Only sets, which carry a merge strategy, combine with those of the page.
	 */
	private static function removePageLevelData( ParserOutput $textOutput ): void {
		foreach ( array_keys( $textOutput->getPageProperties() ) as $name ) {
			$textOutput->unsetPageProperty( (string)$name );
		}

		foreach ( self::PAGE_FLAGS as $flag ) {
			$textOutput->setOutputFlag( $flag, false );
		}

		foreach ( self::extensionDataOf( $textOutput ) as $key => $value ) {
			if ( !is_array( $value ) || !array_key_exists( ParserOutput::MW_MERGE_STRATEGY_KEY, $value ) ) {
				$textOutput->setExtensionData( (string)$key, null );
			}
		}
	}

	/**
	 * A ParserOutput hands over all of its extension data only in its serialization, which is public as jsonSerialize()
	 * on MediaWiki 1.43 and as toJsonArray() since 1.44.
	 */
	private static function extensionDataOf( ParserOutput $output ): array {
		$serialization = method_exists( $output, 'jsonSerialize' ) ? $output->jsonSerialize() : $output->toJsonArray();

		return $serialization['ExtensionData'] ?? [];
	}

	/**
	 * What the HTML of the text needs on the page: its modules, and the head items and config vars of its parse, such as
	 * those Semantic MediaWiki holds for the page until a parse takes them. The rest of the HTML metadata, such as the
	 * title, indicators and table of contents, is the page's own.
	 */
	private static function htmlMetaDataOf( ParserOutput $textOutput ): ParserOutput {
		$htmlMetaData = new ParserOutput();

		foreach ( $textOutput->getHeadItems() as $tag => $headItem ) {
			$htmlMetaData->addHeadItem( $headItem, is_string( $tag ) ? $tag : false );
		}

		$htmlMetaData->addModules( $textOutput->getModules() );
		$htmlMetaData->addModuleStyles( $textOutput->getModuleStyles() );

		// Along with its merge strategy, so that core joins a set, such as the errors Scribunto shows details of, with
		// the set of the page.
		foreach ( $textOutput->getJsConfigVars( true ) as $name => $value ) {
			$htmlMetaData->setJsConfigVar( (string)$name, $value );
		}

		return $htmlMetaData;
	}

}
