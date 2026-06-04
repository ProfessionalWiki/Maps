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

}
