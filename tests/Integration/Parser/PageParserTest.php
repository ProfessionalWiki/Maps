<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\OutputAddingParserOptions;
use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\RecordedLinks;
use Maps\Tests\Util\RefusingParserOptions;
use Maps\Tests\Util\TestFactory;
use ArrayObject;
use MediaWiki\Content\WikitextContent;
use MediaWiki\Debug\MWDebug;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Parser\ParserOutputFlags;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

/**
 * Needs the database: the maps use templates and a GeoJSON page that the tests create.
 *
 * @covers \Maps\Map\DisplayMap\DisplayMapRenderer
 * @covers \Maps\Presentation\WikitextParser
 * @covers \Maps\DataAccess\GeoJsonFetcher
 * @covers \Maps\LeafletService
 */
class PageParserTest extends TestCase {

	private const TEMPLATE = 'Template:PageParserTest template';
	private const TEMPLATE_TEXT = 'Text of a template';
	private const OTHER_TEMPLATE = 'Template:PageParserTest other template';
	private const REFUSED_TEMPLATE = 'Template:PageParserTest refused';
	private const REFUSED_TEMPLATE_TEXT = 'Text of a template the parser options refuse';
	private const OTHER_REFUSED_TEMPLATE = 'Template:PageParserTest other refused';
	private const GEO_JSON_PAGE_NAME = 'PageParserTest';
	private const GEO_JSON_PAGE = 'GeoJson:' . self::GEO_JSON_PAGE_NAME;
	private const GEO_JSON_FEATURE = 'Feature of the PageParserTest GeoJSON page';

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		PageCreator::instance()->createPage( self::TEMPLATE, self::TEMPLATE_TEXT );
		PageCreator::instance()->createPage( self::OTHER_TEMPLATE, 'Text of another template' );
		PageCreator::instance()->createPage( self::REFUSED_TEMPLATE, self::REFUSED_TEMPLATE_TEXT );

		PageCreator::instance()->createGeoJsonPage( self::GEO_JSON_PAGE_NAME, self::GEO_JSON_FEATURE );
	}

	protected function setUp(): void {
		parent::setUp();

		MapsTestFactory::newTestInstance();
	}

	private function render( string $wikitext ): ParserOutput {
		return TestFactory::newInstance()->renderPage( $wikitext, ParserOptions::newFromAnon() );
	}

	private function renderRefusing( string $refusedPageName, string $wikitext ): ParserOutput {
		return TestFactory::newInstance()->renderPage( $wikitext, RefusingParserOptions::refusing( $refusedPageName ) );
	}

	private function renderAddingOnInclude( callable $addToOutput, string $wikitext ): ParserOutput {
		return TestFactory::newInstance()->renderPage( $wikitext, OutputAddingParserOptions::addingOnInclude( $addToOutput ) );
	}

	public function testMarkerTextLeavesOutPagesTheParserOptionsRefuse(): void {
		$html = $this->renderRefusing(
			self::REFUSED_TEMPLATE,
			'<display_map>1,1~Title~{{PageParserTest template}} {{PageParserTest refused}}</display_map>'
		)->getContentHolderText();

		$this->assertStringContainsString( self::TEMPLATE_TEXT, $html );
		$this->assertStringNotContainsString( self::REFUSED_TEMPLATE_TEXT, $html );
	}

	public function testPagesLeftOutOfThePageAndOfItsMarkerTextAreAllNoted(): void {
		$output = TestFactory::newInstance()->renderPage(
			'{{PageParserTest refused}}<display_map>1,1~Title~{{PageParserTest other refused}}</display_map>',
			RefusingParserOptions::refusing( self::REFUSED_TEMPLATE, self::OTHER_REFUSED_TEMPLATE )
		);

		$this->assertEqualsCanonicalizing(
			[ self::REFUSED_TEMPLATE, self::OTHER_REFUSED_TEMPLATE ],
			array_keys( $output->getExtensionData( RefusingParserOptions::REFUSED_PAGES ) ?? [] )
		);
	}

	public function testTemplateUsedInMarkerTextIsRecordedOnThePage(): void {
		$output = $this->render( '<display_map>1,1~Title~{{PageParserTest template}}</display_map>' );

		$this->assertContains( self::TEMPLATE, RecordedLinks::templatesOf( $output ) );
	}

	/**
	 * @dataProvider mapTextLinkProvider
	 */
	public function testLinkInMapTextIsRecordedOnThePage( string $wikitext ): void {
		$this->assertContains( 'PageParserTest link target', RecordedLinks::linksOf( $this->render( $wikitext ) ) );
	}

	public static function mapTextLinkProvider(): iterable {
		yield 'marker text' => [ '{{#display_map:1,1~Title~[[PageParserTest link target]]}}' ];
		yield 'inline label' => [ '{{#display_map:1,1~Title~Text~ ~ ~[[PageParserTest link target]]}}' ];
		yield 'shape text' => [ '{{#display_map:lines=1,1:2,2~Line title~[[PageParserTest link target]]}}' ];
	}

	public function testGalleryInMarkerTextGetsItsStylesAndScriptOnThePage(): void {
		$output = $this->render(
			'<display_map>1,1~Title~<gallery mode="packed">File:PageParserTest.png</gallery></display_map>'
		);

		$this->assertContains( 'mediawiki.page.gallery.styles', $output->getModuleStyles() );
		$this->assertContains( 'mediawiki.page.gallery', $output->getModules() );
	}

	/**
	 * Semantic MediaWiki, for one, holds head items such as the data of a chart for the page until a parse takes them,
	 * which can be the parse of marker text.
	 */
	public function testHeadItemForMarkerTextIsAddedToThePage(): void {
		$output = $this->renderAddingOnInclude(
			static fn ( ParserOutput $output ) => $output->addHeadItem( '<meta name="maps-test">', 'maps-test' ),
			'<display_map>1,1~Title~{{PageParserTest template}}</display_map>'
		);

		$this->assertArrayHasKey( 'maps-test', $output->getHeadItems() );
	}

	public function testConfigVarSetsOfThePageAndItsMarkerTextAreJoined(): void {
		$output = $this->renderAddingOnInclude(
			static fn ( ParserOutput $output, string $pageName ) => $output->appendJsConfigVar( 'mapsTest', $pageName ),
			'{{PageParserTest template}}<display_map>1,1~Title~{{PageParserTest other template}}</display_map>'
		);

		$this->assertEqualsCanonicalizing(
			[ self::TEMPLATE, self::OTHER_TEMPLATE ],
			array_keys( $output->getJsConfigVars()['mapsTest'] ?? [] )
		);
	}

	public function testIndicatorInMarkerTextLeavesThePageAlone(): void {
		$output = $this->render( '<display_map>1,1~Title~<indicator name="maps-test">Indicator</indicator></display_map>' );

		$this->assertArrayNotHasKey( 'maps-test', $output->getIndicators() );
	}

	public function testTimeInMarkerTextShortensThePageCacheExpiry(): void {
		$output = $this->render( '<display_map>1,1~Title~{{CURRENTTIME}}</display_map>' );

		$this->assertTrue( $output->hasReducedExpiry() );
	}

	public function testSortKeyInMarkerTextLeavesThePageAlone(): void {
		$output = $this->render( '<display_map>1,1~Title~{{DEFAULTSORT:Sort key in marker text}}</display_map>' );

		$this->assertNull( $output->getPageProperty( 'defaultsort' ) );
	}

	/**
	 * Extensions such as Widgets and PageImages collect data about the page in extension data, adding each item to
	 * what the page has so far.
	 */
	public function testDataThatExtensionsCollectAboutThePageSurvivesMarkerText(): void {
		$output = $this->renderAddingOnInclude(
			static fn ( ParserOutput $output, string $pageName ) => $output->setExtensionData(
				'maps-test-collected',
				[ ...( $output->getExtensionData( 'maps-test-collected' ) ?? [] ), $pageName ]
			),
			'{{PageParserTest template}}<display_map>1,1~Title~{{PageParserTest other template}}</display_map>'
		);

		$this->assertSame( [ self::TEMPLATE ], $output->getExtensionData( 'maps-test-collected' ) );
	}

	public function testSectionEditLinksSwitchInMarkerTextLeavesThePageAlone(): void {
		$output = $this->render( '<display_map>1,1~Title~__NOEDITSECTION__</display_map>' );

		$this->assertFalse( $output->getOutputFlag( ParserOutputFlags::NO_SECTION_EDIT_LINKS ) );
	}

	/**
	 * Pages in content namespaces ignore the switch, so the page is in another namespace.
	 */
	public function testIndexSwitchInMarkerTextLeavesThePageAlone(): void {
		$output = MediaWikiServices::getInstance()->getContentRenderer()->getParserOutput(
			new WikitextContent( '<display_map>1,1~Title~__NOINDEX__</display_map>' ),
			Title::newFromText( 'Project:Maps test page' ),
			null,
			ParserOptions::newFromAnon()
		);

		$this->assertSame( '', $output->getIndexPolicy() );
	}

	public function testNowikiInMarkerTextIsShownAsText(): void {
		$output = $this->render( '{{#display_map:1,1~Title~<nowiki>* [[Not a link]]</nowiki>}}' );

		$this->assertStringContainsString( '* [[Not a link]]', $output->getContentHolderText() );
	}

	public function testExtensionTagInMarkerTextIsRendered(): void {
		$output = $this->render( '{{#display_map:1,1~Title~<pre>Preformatted</pre>}}' );

		$this->assertStringContainsString( 'Preformatted', $output->getContentHolderText() );
	}

	public function testExtensionTagInNowikiInMarkerTextIsRendered(): void {
		$output = $this->render( '{{#display_map:1,1~Title~{{#tag:nowiki|Before {{#tag:pre|Preformatted}}}}}}' );

		$this->assertStringContainsString( 'Preformatted', $output->getContentHolderText() );
	}

	/**
	 * Link placeholders belong to the legacy parser, which is no longer the default on MediaWiki master.
	 */
	public function testLinkInAGalleryCaptionInMarkerTextKeepsItsTarget(): void {
		$options = ParserOptions::newFromAnon();
		$options->setOption( 'useParsoid', false );

		$output = TestFactory::newInstance()->renderPage(
			'{{#display_map:1,1~Title~[[PageParserTest link target]] ' .
				'<gallery>File:PageParserTest.png|[[PageParserTest caption target]]</gallery>}}',
			$options
		);

		$this->assertStringContainsString( 'title=PageParserTest_caption_target', $output->getContentHolderText() );
	}

	/**
	 * A tag can hand the page wikitext that the page leaves unparsed, such as another tag, which the parse of the
	 * marker text then expands.
	 */
	public function testStripMarkersOfMarkerTextKeepTheirContentWhenItsParseAddsItsOwn(): void {
		$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
		$initialization = $hookContainer->scopedRegister( 'ParserFirstCallInit', static function ( Parser $parser ): void {
			$parser->setHook( 'mapstestecho', static fn ( $content ): string => (string)$content );
			$parser->setHook( 'mapstestunparsed', static fn ( $content ): array => [ $content, 'markerType' => 'none' ] );
		} );

		$html = MediaWikiServices::getInstance()->getParserFactory()->create()->parse(
			'{{#display_map:1,1~Title~<mapstestecho>Expanded by the page</mapstestecho> ' .
				'<mapstestunparsed><mapstestecho>Expanded by the marker text</mapstestecho></mapstestunparsed>}}',
			Title::newFromText( TestFactory::PAGE_NAME ),
			ParserOptions::newFromAnon()
		)->getContentHolderText();

		$this->assertStringContainsString( 'Expanded by the page', $html );
	}

	/**
	 * TemplateStyles before MediaWiki 1.45, for one, keeps the styles a page uses in a property of the page's parser,
	 * which MediaWiki has deprecated, and forgets them when a parse starts.
	 */
	public function testStateThatExtensionsKeepOnThePageParserSurvivesMarkerText(): void {
		$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
		$initialization = $hookContainer->scopedRegister( 'ParserFirstCallInit', static function ( Parser $parser ): void {
			$parser->mapsTestState = new ArrayObject();
			$parser->setHook( 'mapstestremember', static function ( $content, array $attributes, Parser $parser ): string {
				$parser->mapsTestState[] = 'Remembered by the page';
				return '';
			} );
			$parser->setHook( 'mapstestrecall', static fn ( $content, array $attributes, Parser $parser ): string =>
				implode( ' ', $parser->mapsTestState->getArrayCopy() )
			);
		} );
		$parseStart = $hookContainer->scopedRegister( 'ParserClearState', static function ( Parser $parser ): void {
			if ( isset( $parser->mapsTestState ) ) {
				$parser->mapsTestState->exchangeArray( [] );
			}
		} );
		MWDebug::filterDeprecationForTest( '/Parser::\$mapsTestState /' );

		try {
			$html = MediaWikiServices::getInstance()->getParserFactory()->create()->parse(
				'<mapstestremember/><display_map>1,1~Title~Text</display_map><mapstestrecall/>',
				Title::newFromText( TestFactory::PAGE_NAME ),
				ParserOptions::newFromAnon()
			)->getContentHolderText();
		} finally {
			MWDebug::clearDeprecationFilters();
		}

		$this->assertStringContainsString( 'Remembered by the page', $html );
	}

	public function testWidgetInMarkerTextIsShownInThePopup(): void {
		$html = $this->renderWithWidgetsStandIn( '<display_map>1,1~Title~<mapstestwidget/> popup</display_map>' );

		$this->assertStringContainsString( 'HELLO-WIDGET', $html );
	}

	public function testWidgetOfThePageSurvivesAWidgetInMarkerText(): void {
		$html = $this->renderWithWidgetsStandIn(
			'<mapstestwidget/><display_map>1,1~Title~<mapstestwidget/> popup</display_map>'
		);

		$this->assertStringContainsString(
			'<span class="w-hello">HELLO-WIDGET</span>',
			strstr( $html, '<div id="map_leaflet', true )
		);
	}

	/**
	 * The page expands the arguments of a parser function, and so the widgets in them.
	 */
	public function testWidgetHtmlInMapDataIsEscaped(): void {
		$html = $this->renderWithWidgetsStandIn( '{{#display_map:1,1~Title~<mapstestwidget/> popup}}' );

		$this->assertStringContainsString( 'HELLO-WIDGET', $html );
		$this->assertStringNotContainsString( '<span class="w-hello">', $html );
	}

	/**
	 * Stands in for the Widgets extension: a widget leaves a marker, which the widget's HTML replaces when the parse of
	 * the page ends.
	 */
	private function renderWithWidgetsStandIn( string $wikitext ): string {
		$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
		$initialization = $hookContainer->scopedRegister( 'ParserFirstCallInit', static function ( Parser $parser ): void {
			$parser->setHook( 'mapstestwidget', static function ( $content, array $attributes, Parser $parser ): string {
				$key = '-' . wfRandomString( 16 );
				$widgets = (array)$parser->getOutput()->getExtensionData( 'widgetReplacements' );
				$widgets[$key] = '<span class="w-hello">HELLO-WIDGET</span>';
				$parser->getOutput()->setExtensionData( 'widgetReplacements', $widgets );
				return "\u{FFFC}START_WIDGET{$key}END_WIDGET";
			} );
		} );
		$parseEnd = $hookContainer->scopedRegister( 'ParserAfterTidy', static function ( Parser $parser, &$text ): void {
			$widgets = (array)$parser->getOutput()->getExtensionData( 'widgetReplacements' );
			$text = preg_replace_callback(
				"/\u{FFFC}START_WIDGET(\"?'?-[a-z0-9]{16})END_WIDGET/",
				static fn ( array $marker ): string => $widgets[$marker[1]],
				$text
			);
		} );

		return MediaWikiServices::getInstance()->getParserFactory()->create()->parse(
			$wikitext,
			Title::newFromText( TestFactory::PAGE_NAME ),
			ParserOptions::newFromAnon()
		)->getContentHolderText();
	}

	public function testPageNameInMarkerTextIsThatOfThePage(): void {
		$output = $this->render( '<display_map>1,1~Title~{{PAGENAME}}</display_map>' );

		$this->assertStringContainsString( TestFactory::PAGE_NAME, $output->getContentHolderText() );
	}

	/**
	 * Parsoid has the legacy parser expand parser functions in this mode.
	 */
	public function testExtensionTagInMarkerTextIsSanitizedWhenThePageIsOnlyPreprocessed(): void {
		$wikitext = MediaWikiServices::getInstance()->getParserFactory()->create()->preprocess(
			'{{#display_map:1,1~Title~<pre onmouseover="alert(1)">Preformatted</pre>}}',
			Title::newFromText( TestFactory::PAGE_NAME ),
			ParserOptions::newFromAnon()
		);

		$this->assertStringContainsString( 'Preformatted', $wikitext );
		$this->assertStringNotContainsString( 'onmouseover', $wikitext );
	}

	public function testParserOptionUsedInMarkerTextVariesThePageCache(): void {
		$output = $this->render( '<display_map>1,1~Title~{{int:mainpage}}</display_map>' );

		$this->assertContains( 'userlang', $output->getUsedOptions() );
	}

	public function testParserOptionUsedAfterAMapVariesThePageCache(): void {
		$output = $this->render( "{{#display_map:1,1~Title~Text}}\n{{int:mainpage}}" );

		$this->assertContains( 'userlang', $output->getUsedOptions() );
	}

	public function testGeoJsonLeavesOutPagesTheParserOptionsRefuse(): void {
		$output = $this->renderRefusing( self::GEO_JSON_PAGE, '{{#display_map:geojson=PageParserTest}}' );

		$this->assertStringNotContainsString( self::GEO_JSON_FEATURE, $output->getContentHolderText() );
	}

	/**
	 * The link tables come from one render: an edit of the GeoJSON page must also reach the renders that showed it.
	 */
	public function testGeoJsonPageTheParserOptionsRefuseIsStillRecordedOnThePage(): void {
		$output = $this->renderRefusing( self::GEO_JSON_PAGE, '{{#display_map:geojson=PageParserTest}}' );

		$this->assertContains( self::GEO_JSON_PAGE, RecordedLinks::templatesOf( $output ) );
	}

}
