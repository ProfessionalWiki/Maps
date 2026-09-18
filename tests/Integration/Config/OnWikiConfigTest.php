<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Config;

use FileFetcher\StubFileFetcher;
use Maps\LeafletService;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * Verifies that values set on the MediaWiki:Maps config page actually reach the settings consumers,
 * end to end, through the effective-settings lookup.
 *
 * @covers \Maps\Config\EffectiveSettings
 */
class OnWikiConfigTest extends TestCase {

	private const GEO_JSON_URL = 'https://example.com/points.geojson';
	private const GEO_JSON_URL_FEATURE = 'Feature of the file on the other host';

	private bool $originalAllowExternalDataFiles;

	protected function setUp(): void {
		parent::setUp();

		$this->originalAllowExternalDataFiles = $GLOBALS['egMapsAllowExternalDataFiles'] ?? false;
	}

	protected function tearDown(): void {
		$GLOBALS['egMapsAllowExternalDataFiles'] = $this->originalAllowExternalDataFiles;
		MapsTestFactory::$wikiConfig = null;
		MapsTestFactory::$geoJsonFileFetcher = null;
		MapsTestFactory::newTestInstance();

		parent::tearDown();
	}

	private function parseWithWikiConfig( array $wikiConfig, string $wikitext ): string {
		MapsTestFactory::$wikiConfig = $wikiConfig;
		MapsTestFactory::newTestInstance();

		return TestFactory::newInstance()->parse( $wikitext );
	}

	public function testWikiMapWidthReachesTheRenderedMapHtml(): void {
		$html = $this->parseWithWikiConfig(
			[ 'general' => [ 'mapWidth' => '640px' ] ],
			'{{#display_map:1,1}}'
		);

		$this->assertStringContainsString( 'width: 640px;', $html );
	}

	public function testWikiGoogleZoomReachesTheRenderedMapData(): void {
		$html = $this->parseWithWikiConfig(
			[ 'googleMaps' => [ 'zoom' => 3 ] ],
			'{{#google_maps:1,1}}'
		);

		$this->assertStringContainsString( htmlspecialchars( '"zoom":3' ), $html );
	}

	public function testWikiExternalDataFilePolicyReachesTheRenderedMapData(): void {
		$this->setPhpSetting( false );

		$html = $this->parseWithWikiConfig(
			[ 'general' => [ 'allowExternalDataFiles' => true ] ],
			'{{#google_maps:kml=https://example.com/points.kml}}'
		);

		$this->assertStringContainsString(
			htmlspecialchars( '"kml":["https://example.com/points.kml"]' ),
			$html
		);
	}

	public function testWikiExternalDataFilePolicyLetsGeoJsonUrlsBeFetched(): void {
		$this->setPhpSetting( false );
		$this->stubTheGeoJsonUrl();

		$html = $this->parseWithWikiConfig(
			[ 'general' => [ 'allowExternalDataFiles' => true ] ],
			'{{#display_map:geojson=' . self::GEO_JSON_URL . '}}'
		);

		$this->assertStringContainsString( htmlspecialchars( self::GEO_JSON_URL_FEATURE ), $html );
	}

	public function testWikiExternalDataFilePolicyStopsGeoJsonUrlsFromBeingFetched(): void {
		$this->setPhpSetting( true );
		$this->stubTheGeoJsonUrl();

		$html = $this->parseWithWikiConfig(
			[ 'general' => [ 'allowExternalDataFiles' => false ] ],
			'{{#display_map:geojson=' . self::GEO_JSON_URL . '}}'
		);

		$this->assertStringContainsString( htmlspecialchars( '"geojson":[]' ), $html );
	}

	private function setPhpSetting( bool $allow ): void {
		$GLOBALS['egMapsAllowExternalDataFiles'] = $allow;
		MapsTestFactory::newTestInstance();
	}

	/**
	 * Nothing is fetched over the network in these tests: this replaces the file fetcher that the
	 * GeoJson urls would go to.
	 */
	private function stubTheGeoJsonUrl(): void {
		MapsTestFactory::$geoJsonFileFetcher = new StubFileFetcher( json_encode( [
			'type' => 'FeatureCollection',
			'features' => [
				[
					'type' => 'Feature',
					'geometry' => [ 'type' => 'Point', 'coordinates' => [ 4.35, 50.85 ] ],
					'properties' => [ 'title' => self::GEO_JSON_URL_FEATURE ],
				],
			],
		] ) );
	}

	/**
	 * The allowExternalKml of Maps 14.2 is left on config pages until someone renames it. It no
	 * longer decides anything, rather than deciding for its successor.
	 */
	public function testStaleAllowExternalKmlKeyDoesNotAllowExternalDataFiles(): void {
		$this->setPhpSetting( false );

		$html = $this->parseWithWikiConfig(
			[ 'general' => [ 'allowExternalKml' => true ] ],
			'{{#google_maps:kml=https://example.com/points.kml}}'
		);

		$this->assertStringContainsString( htmlspecialchars( '"kml":[]' ), $html );
	}

	public function testWikiLeafletLayerDefinitionReachesTheRenderedMapData(): void {
		$html = $this->parseWithWikiConfig(
			[ 'leaflet' => [ 'layerDefinitions' => [
				'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
			] ] ],
			'{{#leaflet:layers=Historic}}'
		);

		$this->assertStringContainsString(
			htmlspecialchars( '"url":"https://tiles.example/{z}/{x}/{y}.png"' ),
			$html
		);
	}

	/**
	 * @covers \Maps\MapsFactory::getLeafletLayerDefinitions
	 */
	public function testLeafletLayerDefinitionsAccessorSeesWikiDefinitions(): void {
		MapsTestFactory::$wikiConfig = [ 'leaflet' => [ 'layerDefinitions' => [
			'FromWiki' => [ 'url' => 'https://wiki.example/{z}/{x}/{y}.png' ],
		] ] ];
		$factory = MapsTestFactory::newTestInstance();

		$definitions = $factory->getLeafletLayerDefinitions()->getDefinitions( [ 'FromWiki' ] );

		$this->assertSame( 'https://wiki.example/{z}/{x}/{y}.png', $definitions['FromWiki']['url'] );
	}

	public function testWikiLeafletDefaultZoomReachesTheParameterDefault(): void {
		MapsTestFactory::$wikiConfig = [ 'leaflet' => [ 'defaultZoom' => 7 ] ];
		$factory = MapsTestFactory::newTestInstance();

		$service = new LeafletService( new InMemoryImageRepository(), $factory->getEffectiveSettings() );

		$this->assertSame( 7, $service->getParameterInfo()['defzoom']['default'] );
	}

}
