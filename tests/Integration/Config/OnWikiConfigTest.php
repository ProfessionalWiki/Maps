<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Config;

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

	protected function tearDown(): void {
		MapsTestFactory::$wikiConfig = null;
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

	public function testWikiLeafletDefaultZoomReachesTheParameterDefault(): void {
		MapsTestFactory::$wikiConfig = [ 'leaflet' => [ 'defaultZoom' => 7 ] ];
		$factory = MapsTestFactory::newTestInstance();

		$service = new LeafletService( new InMemoryImageRepository(), $factory->getEffectiveSettings() );

		$this->assertSame( 7, $service->getParameterInfo()['defzoom']['default'] );
	}

}
