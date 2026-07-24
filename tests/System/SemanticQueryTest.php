<?php

declare( strict_types = 1 );

namespace Maps\Tests\System;

use Maps\DataAccess\PageContentFetcher;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\TestFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Map\SemanticFormat\MapPrinter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SemanticQueryTest extends TestCase {

	private const MISSING_COORDINATES_WARNING = 'none of the properties used in the query';

	/**
	 * @var PageCreator
	 */
	private $pageCreator;

	/**
	 * @var PageContentFetcher
	 */
	private $contentFetcher;

	public function setUp(): void {
		if ( !defined( 'SMW_VERSION' ) ) {
			$this->markTestSkipped( 'SMW is not available' );
		}

		$this->pageCreator = TestFactory::newInstance()->getPageCreator();
		$this->contentFetcher = MapsTestFactory::newTestInstance()->getPageContentFetcher();
	}

	public function testMapQueryContainsMarkersWithInfo() {
		$this->createDataPages();

		$content = $this->getResultForQuery( '{{#ask:[[Coordinates::+]]|?Coordinates|?Description|?URL|format=map}}' );

		$this->assertStringContainsString( '<div id="map_', $content );
		$this->assertStringContainsString( 'Capital of Belgium', $content );
		$this->assertStringContainsString( 'example.com', $content );
	}

	public function testLeafletQueryWithGeoJson() {
		$this->createDataPages();

		// The map data is embedded as an HTML-escaped data attribute, so decode before asserting on the JSON.
		$content = htmlspecialchars_decode(
			$this->getResultForQuery( '{{#ask:[[Coordinates::+]]|?Coordinates|format=leaflet|geojson=TestGeoJson}}' )
		);

		$this->assertStringContainsString( '<div id="map_leaflet_', $content );
		$this->assertStringContainsString( '"GeoJsonSource":"TestGeoJson"', $content );
		$this->assertStringContainsString( '"GeoJsonRevisionId":', $content );
		$this->assertStringContainsString( '"geojson":{"type":"FeatureCollection"', $content );
	}

	public function testGoogleMapsQueryContainsImageOverlayData() {
		$this->createDataPages();

		$content = htmlspecialchars_decode(
			$this->getResultForQuery(
				'{{#ask:[[Coordinates::+]]|?Coordinates|format=googlemaps3'
				. '|imageoverlays=52.1,13.1:51.9,12.9:TestOverlay.png}}'
			)
		);

		$this->assertStringContainsString( '"image":"TestOverlay.png"', $content );
		$this->assertStringContainsString( '"ne":{"lon":13.1,"lat":52.1}', $content );
		$this->assertStringContainsString( '"sw":{"lon":12.9,"lat":51.9}', $content );
	}

	private function getResultForQuery( string $query ): string {
		$this->pageCreator->createPage(
			'MapQuery',
			$query
		);

		$title = Title::newFromText( 'MapQuery' );
		$content = $this->contentFetcher->getPageContent( 'MapQuery' );

		return MediaWikiServices::getInstance()->getContentRenderer()
			->getParserOutput( $content, $title )
			->getText();
	}

	private function createDataPages() {
		$this->pageCreator->createPage(
			'GeoJson:TestGeoJson',
			'{"type": "FeatureCollection", "features": []}'
		);

		$this->pageCreator->createPage(
			'Property:Coordinates',
			'[[Has type::Geographic coordinate|geographic coordinate]]'
		);

		$this->pageCreator->createPage(
			'Property:Description',
			'[[Has type::Text]]'
		);

		$this->pageCreator->createPage(
			'Property:URL',
			'[[Has type::URL]]'
		);

		$this->pageCreator->createPage(
			'Berlin',
			'[[Coordinates::52° 31\' 0", 13° 24\' 0"]] [[Description::Capital of Germany]] [[URL::http://example.com/Berlin]]'
		);

		$this->pageCreator->createPage(
			'Brussels',
			'[[Coordinates::50° 51\' 1", 4° 21\' 6"]] [[Description::Capital of Belgium]]'
		);

		$this->pageCreator->createPage(
			'Hamburg',
			'[[Coordinates::53° 33\' 4", 9° 59\' 37"]]'
		);
	}

	public function testMapQueryWithTemplate() {
		$this->createDataPages();

		$content = $this->getResultForQuery( '{{#ask:[[Coordinates::+]]|?Coordinates|format=map|template=Whatever}}' );

		$this->assertStringContainsString( '<div id="map_', $content );
	}

	public function testQueryWithoutCoordinatePrintoutShowsTypeWarning() {
		$this->pageCreator->createPage(
			'Property:TextualCoordinates',
			'[[Has type::Text]]'
		);

		$this->pageCreator->createPage(
			'TextualCoordinatesPage',
			'[[TextualCoordinates::52° 31\' 0", 13° 24\' 0"]]'
		);

		$content = $this->getResultForQuery( '{{#ask:[[TextualCoordinates::+]]|?TextualCoordinates|format=map}}' );

		$this->assertStringNotContainsString( '<div id="map_', $content );
		$this->assertStringContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

	public function testQueryWithCoordinatePrintoutWithoutValuesShowsNoWarning() {
		$this->createDataPages();

		$this->pageCreator->createPage(
			'PageWithoutCoordinates',
			'[[Description::A page without coordinates]]'
		);

		$content = $this->getResultForQuery( '{{#ask:[[Description::A page without coordinates]]|?Coordinates|format=map}}' );

		$this->assertStringNotContainsString( '<div id="map_', $content );
		$this->assertStringNotContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

	public function testAjaxQueryWithoutCoordinatePrintoutShowsNoWarning() {
		$this->createDataPages();

		$content = $this->getResultForQuery(
			'{{#ask:[[Description::+]]|?Description|format=map|ajaxcoordproperty=Coordinates|ajaxquery=TestQuery}}'
		);

		$this->assertStringNotContainsString( '<div id="map_', $content );
		$this->assertStringNotContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

	public function testHalfConfiguredAjaxQueryWithoutCoordinatePrintoutShowsWarning() {
		$this->createDataPages();

		// The ajax JS only activates when both ajaxcoordproperty and ajaxquery are set,
		// so setting only one of them never loads markers and the warning still applies.
		$content = $this->getResultForQuery( '{{#ask:[[Description::+]]|?Description|format=map|ajaxcoordproperty=Coordinates}}' );

		$this->assertStringContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

	public function testGeoJsonQueryWithoutCoordinatePrintoutShowsNoWarning() {
		$this->createDataPages();

		$content = $this->getResultForQuery( '{{#ask:[[Description::+]]|?Description|format=leaflet|geojson=TestGeoJson}}' );

		$this->assertStringNotContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

	public function testGoogleMaps3KmlQueryWithoutCoordinatePrintoutShowsNoWarning() {
		$this->createDataPages();

		$content = $this->getResultForQuery( '{{#ask:[[Description::+]]|?Description|format=googlemaps3|kml=TestFile.kml}}' );

		$this->assertStringNotContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

	public function testQueryWithRecordPrintoutShowsNoWarning() {
		$this->createDataPages();

		$this->pageCreator->createPage(
			'Property:AddressRecord',
			'[[Has type::Record]] [[Has fields::Description;Coordinates]]'
		);

		$this->pageCreator->createPage(
			'PageWithRecordProperty',
			'[[Description::Tagged for the record printout test]]'
		);

		$content = $this->getResultForQuery( '{{#ask:[[Description::Tagged for the record printout test]]|?AddressRecord|format=map}}' );

		$this->assertStringNotContainsString( self::MISSING_COORDINATES_WARNING, $content );
	}

}
