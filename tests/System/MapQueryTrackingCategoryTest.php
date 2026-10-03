<?php

declare( strict_types = 1 );

namespace Maps\Tests\System;

use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\DeprecationWarnings;
use Maps\Tests\Util\PageCreator;
use MediaWiki\Content\WikitextContent;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;
use SMWQueryProcessor;

/**
 * Needs the database: the queries need a page with coordinates in the Semantic MediaWiki store.
 *
 * @covers \Maps\Map\SemanticFormat\MapPrinter
 */
class MapQueryTrackingCategoryTest extends TestCase {

	private const INLINE_MAP_QUERY = '{{#ask:[[Coordinates::+]]|?Coordinates|format=map}}';

	private mixed $originalEnableCategory;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		if ( !defined( 'SMW_VERSION' ) ) {
			self::markTestSkipped( 'SMW is not available' );
		}

		PageCreator::instance()->createPage(
			'Property:Coordinates',
			'[[Has type::Geographic coordinate|geographic coordinate]]'
		);

		PageCreator::instance()->createPage(
			'MapQueryTrackingCategoryTest location',
			'[[Coordinates::52° 31\' 0", 13° 24\' 0"]]'
		);
	}

	protected function setUp(): void {
		parent::setUp();

		$this->originalEnableCategory = $GLOBALS['egMapsEnableCategory'];
		$this->setTrackingCategoryEnabled( true );
	}

	protected function tearDown(): void {
		$this->setTrackingCategoryEnabled( $this->originalEnableCategory );

		parent::tearDown();
	}

	private function setTrackingCategoryEnabled( mixed $enabled ): void {
		$GLOBALS['egMapsEnableCategory'] = $enabled;
		MapsTestFactory::newTestInstance();
	}

	private function pageTitle(): Title {
		return Title::newFromText( 'MapQueryTrackingCategoryTest' );
	}

	private function trackingCategory(): string {
		return MediaWikiServices::getInstance()->getTrackingCategories()
			->resolveTrackingCategory( 'maps-tracking-category', $this->pageTitle() )
			->getDBkey();
	}

	public function testMapQueryOnAPageAddsTheTrackingCategory(): void {
		$this->assertContains( $this->trackingCategory(), $this->renderPage()->getCategoryNames() );
	}

	public function testMapQueryOnAPageAddsNoTrackingCategoryWhenDisabled(): void {
		$this->setTrackingCategoryEnabled( false );

		$this->assertNotContains( $this->trackingCategory(), $this->renderPage()->getCategoryNames() );
	}

	private function renderPage(): ParserOutput {
		return MediaWikiServices::getInstance()->getContentRenderer()->getParserOutput(
			new WikitextContent( self::INLINE_MAP_QUERY ),
			$this->pageTitle()
		);
	}

	/**
	 * Content handlers of other extensions, such as NativeMarkdown, expand wikitext on a parser
	 * instance of their own while MediaWiki's main parser is idle.
	 */
	public function testMapQueryParsedOnAnotherParserInstanceAddsTheTrackingCategory(): void {
		$parserOutput = MediaWikiServices::getInstance()->getParserFactory()->create()->parse(
			self::INLINE_MAP_QUERY,
			$this->pageTitle(),
			ParserOptions::newFromAnon()
		);

		$this->assertContains( $this->trackingCategory(), $parserOutput->getCategoryNames() );
	}

	/**
	 * Parsoid expands the query on a legacy parser of its own. MediaWiki 1.47 updates links, and so
	 * category membership, with Parsoid by default.
	 */
	public function testMapQueryRenderedByParsoidAddsTheTrackingCategory(): void {
		$options = ParserOptions::newFromAnon();
		$options->setUseParsoid();

		$parserOutput = MediaWikiServices::getInstance()->getParsoidParserFactory()->create()->parse(
			self::INLINE_MAP_QUERY,
			$this->pageTitle(),
			$options
		);

		$this->assertContains( $this->trackingCategory(), $parserOutput->getCategoryNames() );
	}

	/**
	 * Special:Ask renders a result format outside any parse, possibly before the main parser parsed
	 * anything in the request; SMW then hands the printer that parser.
	 */
	public function testMapShownOnSpecialAskEmitsNoDeprecationWarnings(): void {
		$this->assertRendersWithoutDeprecationWarnings( SMWQueryProcessor::SPECIAL_PAGE );
	}

	/**
	 * Extensions such as Semantic Compound Queries render inline queries with whichever parser SMW
	 * last held, which can be one that never parsed.
	 */
	public function testInlineMapQueryWithAParserThatNeverParsedEmitsNoDeprecationWarnings(): void {
		$this->assertRendersWithoutDeprecationWarnings( SMWQueryProcessor::INLINE_QUERY );
	}

	/**
	 * Earlier queries in this process have used the main parser, so a parser that never parsed
	 * stands in for it.
	 */
	private function assertRendersWithoutDeprecationWarnings( int $context ): void {
		$services = MediaWikiServices::getInstance();
		$mainParser = $services->getParser();
		$this->replaceMainParser( $services->getParserFactory()->create() );
		$html = '';

		try {
			$warnings = DeprecationWarnings::during( function () use ( $context, &$html ) {
				$html = $this->renderQueryResult( $context );
			} );
		} finally {
			$this->replaceMainParser( $mainParser );
		}

		$this->assertStringContainsString( 'data-mw-maps-mapdata=', $html );
		$this->assertSame( [], $warnings );
	}

	/**
	 * Also drops the parser that SMW kept from an earlier query, so that SMW falls back to this one.
	 */
	private function replaceMainParser( Parser $parser ): void {
		$services = MediaWikiServices::getInstance();
		$services->resetServiceForTesting( 'Parser', false );
		$services->redefineService( 'Parser', static fn () => $parser );
		SMWQueryProcessor::setRecursiveTextProcessor();
	}

	private function renderQueryResult( int $context ): string {
		[ $query, $params ] = SMWQueryProcessor::getQueryAndParamsFromFunctionParams(
			[ '[[Coordinates::+]]', '?Coordinates', 'format=map' ],
			SMW_OUTPUT_HTML,
			$context,
			false
		);

		return SMWQueryProcessor::getResultFromQuery( $query, $params, SMW_OUTPUT_HTML, $context );
	}

	public function testMapShownOnSpecialAskLeavesThePreviousParseUntouched(): void {
		$previousOutput = MediaWikiServices::getInstance()->getParser()->parse(
			'Some text',
			$this->pageTitle(),
			ParserOptions::newFromAnon()
		);
		SMWQueryProcessor::setRecursiveTextProcessor();

		$html = $this->renderQueryResult( SMWQueryProcessor::SPECIAL_PAGE );

		$this->assertStringContainsString( 'data-mw-maps-mapdata=', $html );
		$this->assertNotContains( $this->trackingCategory(), $previousOutput->getCategoryNames() );
	}

}
