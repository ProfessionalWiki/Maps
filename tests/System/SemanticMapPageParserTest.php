<?php

declare( strict_types = 1 );

namespace Maps\Tests\System;

use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\RecordedLinks;
use Maps\Tests\Util\RefusingParserOptions;
use Maps\Tests\Util\TestFactory;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\ParserOutput;
use PHPUnit\Framework\TestCase;
use SMW\ParserData;
use SMWQueryProcessor;

/**
 * Needs the database: the queries need a page with coordinates in the Semantic MediaWiki store, and the maps use a
 * popup template and a GeoJSON page that the tests create.
 *
 * @covers \Maps\Map\SemanticFormat\MapPrinter
 * @covers \Maps\SemanticMW\QueryHandler
 * @covers \Maps\Presentation\WikitextParser
 * @covers \Maps\MapsFactory::newRequestParser
 */
class SemanticMapPageParserTest extends TestCase {

	private const POPUP_TEMPLATE = 'Template:SemanticMapPageParserTest popup';
	private const POPUP_TEXT = 'Text of the popup template';
	private const REFUSED_PAGE = 'SemanticMapPageParserTest refused';
	private const REFUSED_TEXT = 'Text of a page the parser options refuse';
	private const GEO_JSON_PAGE_NAME = 'SemanticMapPageParserTest';
	private const GEO_JSON_PAGE = 'GeoJson:' . self::GEO_JSON_PAGE_NAME;
	private const GEO_JSON_FEATURE = 'Feature of the SemanticMapPageParserTest GeoJSON page';

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
			'SemanticMapPageParserTest location',
			'[[Coordinates::52° 31\' 0", 13° 24\' 0"]]'
		);

		PageCreator::instance()->createPage( self::POPUP_TEMPLATE, self::POPUP_TEXT . ' {{:' . self::REFUSED_PAGE . '}}' );
		PageCreator::instance()->createPage( self::REFUSED_PAGE, self::REFUSED_TEXT );

		PageCreator::instance()->createGeoJsonPage( self::GEO_JSON_PAGE_NAME, self::GEO_JSON_FEATURE );
	}

	protected function setUp(): void {
		parent::setUp();

		MapsTestFactory::newTestInstance();
	}

	private function render( string $wikitext ): ParserOutput {
		return TestFactory::newInstance()->renderPage( $wikitext, ParserOptions::newFromAnon() );
	}

	public function testTemplatedPopupLeavesOutPagesTheParserOptionsRefuse(): void {
		$html = TestFactory::newInstance()->renderPage(
			'{{#ask:[[Coordinates::+]]|?Coordinates|format=map|template=SemanticMapPageParserTest popup}}',
			RefusingParserOptions::refusing( self::REFUSED_PAGE )
		)->getContentHolderText();

		$this->assertStringContainsString( self::POPUP_TEXT, $html );
		$this->assertStringNotContainsString( self::REFUSED_TEXT, $html );
	}

	public function testPopupTemplateIsRecordedOnThePage(): void {
		$output = $this->render(
			'{{#ask:[[Coordinates::+]]|?Coordinates|format=map|template=SemanticMapPageParserTest popup}}'
		);

		$this->assertContains( self::POPUP_TEMPLATE, RecordedLinks::templatesOf( $output ) );
	}

	public function testLinkInStaticLocationTextIsRecordedOnThePage(): void {
		$output = $this->render(
			'{{#ask:[[Coordinates::+]]|?Coordinates|format=map|locations=1,1~Title~[[SemanticMapPageParserTest link target]]}}'
		);

		$this->assertContains( 'SemanticMapPageParserTest link target', RecordedLinks::linksOf( $output ) );
	}

	public function testLinkInShapeTextIsRecordedOnThePage(): void {
		$output = $this->render(
			'{{#ask:[[Coordinates::+]]|?Coordinates|format=map|lines=1,1:2,2~Line title~[[SemanticMapPageParserTest shape link target]]}}'
		);

		$this->assertContains( 'SemanticMapPageParserTest shape link target', RecordedLinks::linksOf( $output ) );
	}

	public function testGeoJsonPageIsRecordedOnThePage(): void {
		$output = $this->render(
			'{{#ask:[[Coordinates::+]]|?Coordinates|format=leaflet|geojson=SemanticMapPageParserTest}}'
		);

		$this->assertContains( self::GEO_JSON_PAGE, RecordedLinks::templatesOf( $output ) );
	}

	public function testSemanticDataOfThePageSurvivesMarkerText(): void {
		$output = $this->render( '{{#set:Description=Set before the map}}{{#display_map:1,1~Title~Text}}' );

		$this->assertContains( 'Description', $this->semanticPropertiesOf( $output ) );
	}

	/**
	 * @return string[]
	 */
	private function semanticPropertiesOf( ParserOutput $output ): array {
		return array_map(
			static fn ( $property ): string => $property->getKey(),
			$output->getExtensionData( ParserData::DATA_ID )->getProperties()
		);
	}

	/**
	 * Nothing caches a map on Special:Ask for other readers.
	 */
	public function testMapOnSpecialAskShowsWhatTheRequestingUserMayRead(): void {
		RefusingParserOptions::whileDefaultsRefuseAnonymousReaders( function (): void {
			$this->assertStringContainsString(
				self::GEO_JSON_FEATURE,
				$this->renderOnSpecialAsk( [ '[[Coordinates::+]]', '?Coordinates', 'format=leaflet', 'geojson=SemanticMapPageParserTest' ] )
			);
		} );
	}

	private function renderOnSpecialAsk( array $queryParameters ): string {
		[ $query, $params ] = SMWQueryProcessor::getQueryAndParamsFromFunctionParams(
			$queryParameters,
			SMW_OUTPUT_HTML,
			SMWQueryProcessor::SPECIAL_PAGE,
			false
		);

		return SMWQueryProcessor::getResultFromQuery( $query, $params, SMW_OUTPUT_HTML, SMWQueryProcessor::SPECIAL_PAGE );
	}

}
