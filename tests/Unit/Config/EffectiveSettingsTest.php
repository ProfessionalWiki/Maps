<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\ConfigSchema;
use Maps\Config\EffectiveSettings;
use Maps\Config\WikiConfigSource;
use Maps\Tests\TestDoubles\StubWikiConfigSource;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Maps\Config\EffectiveSettings
 */
class EffectiveSettingsTest extends TestCase {

	private function newSettings( array $phpSettings, ?array $wikiConfig, bool $enabled = true ): EffectiveSettings {
		return new EffectiveSettings(
			$phpSettings,
			ConfigSchema::newDefault(),
			new StubWikiConfigSource( $wikiConfig ),
			$enabled
		);
	}

	public function testReturnsPhpValueWhenThereIsNoWikiConfig(): void {
		$settings = $this->newSettings( [ 'egMapsMapHeight' => 350 ], null );

		$this->assertSame( 350, $settings->get( 'egMapsMapHeight' ) );
	}

	public function testWikiValueReplacesPhpValue(): void {
		$settings = $this->newSettings(
			[ 'egMapsMapHeight' => 350 ],
			[ 'general' => [ 'mapHeight' => 420 ] ]
		);

		$this->assertSame( 420, $settings->get( 'egMapsMapHeight' ) );
	}

	public function testInvalidWikiValueIsIgnored(): void {
		$settings = $this->newSettings(
			[ 'egMapsMapHeight' => 350 ],
			[ 'general' => [ 'mapHeight' => 'not a dimension' ] ]
		);

		$this->assertSame( 350, $settings->get( 'egMapsMapHeight' ) );
	}

	public function testWikiConfigIsIgnoredWhenDisabled(): void {
		$settings = $this->newSettings(
			[ 'egMapsMapHeight' => 350 ],
			[ 'general' => [ 'mapHeight' => 420 ] ],
			false
		);

		$this->assertSame( 350, $settings->get( 'egMapsMapHeight' ) );
	}

	public function testDefaultLayersAreReplacedWholesale(): void {
		$settings = $this->newSettings(
			[ 'egMapsLeafletLayers' => [ 'OpenStreetMap' ] ],
			[ 'leaflet' => [ 'defaultLayers' => [ 'OpenTopoMap', 'Esri.WorldImagery' ] ] ]
		);

		$this->assertSame( [ 'OpenTopoMap', 'Esri.WorldImagery' ], $settings->get( 'egMapsLeafletLayers' ) );
	}

	public function testAvailableLayersAreMergedPerNameWithWikiWinning(): void {
		$settings = $this->newSettings(
			[ 'egMapsLeafletAvailableLayers' => [ 'Kept' => true, 'Disabled' => true ] ],
			[ 'leaflet' => [ 'availableLayers' => [ 'Disabled' => false, 'Added' => true ] ] ]
		);

		$available = $settings->get( 'egMapsLeafletAvailableLayers' );

		$this->assertTrue( $available['Kept'] );
		$this->assertFalse( $available['Disabled'] );
		$this->assertTrue( $available['Added'] );
	}

	public function testLayerDefinitionsAreMergedPerName(): void {
		$settings = $this->newSettings(
			[ 'egMapsLeafletLayerDefinitions' => [ 'Php' => [ 'url' => 'https://tiles.example/php/{z}/{x}/{y}.png' ] ] ],
			[ 'leaflet' => [ 'layerDefinitions' => [ 'Wiki' => [ 'url' => 'https://tiles.example/wiki/{z}/{x}/{y}.png' ] ] ] ]
		);

		$definitions = $settings->get( 'egMapsLeafletLayerDefinitions' );

		$this->assertArrayHasKey( 'Php', $definitions );
		$this->assertArrayHasKey( 'Wiki', $definitions );
	}

	public function testWikiLayerDefinitionWinsOnNameCollision(): void {
		$settings = $this->newSettings(
			[ 'egMapsLeafletLayerDefinitions' => [ 'Shared' => [ 'url' => 'https://tiles.example/php/{z}/{x}/{y}.png' ] ] ],
			[ 'leaflet' => [ 'layerDefinitions' => [ 'Shared' => [ 'url' => 'https://tiles.example/wiki/{z}/{x}/{y}.png' ] ] ] ]
		);

		$this->assertSame(
			'https://tiles.example/wiki/{z}/{x}/{y}.png',
			$settings->get( 'egMapsLeafletLayerDefinitions' )['Shared']['url']
		);
	}

	public function testNumericLayerNameIsPreservedInMerge(): void {
		$settings = $this->newSettings(
			[ 'egMapsLeafletLayerDefinitions' => [] ],
			[ 'leaflet' => [ 'layerDefinitions' => [ '1904' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ] ] ] ]
		);

		$this->assertArrayHasKey( '1904', $settings->get( 'egMapsLeafletLayerDefinitions' ) );
	}

	public function testUnknownGroupsAndKeysHaveNoEffect(): void {
		$settings = $this->newSettings(
			[ 'egMapsMapHeight' => 350 ],
			[ 'bogus' => [ 'x' => 1 ], 'general' => [ 'unknown' => 5 ] ]
		);

		$this->assertSame( 350, $settings->get( 'egMapsMapHeight' ) );
	}

	public function testExcludedSettingIsNeverOverlaid(): void {
		$settings = $this->newSettings(
			[ 'egMapsDefaultService' => 'leaflet' ],
			[ 'general' => [ 'defaultService' => 'googlemaps3' ] ]
		);

		$this->assertSame( 'leaflet', $settings->get( 'egMapsDefaultService' ) );
	}

	public function testOverlayIsMemoizedSoTheWikiPageIsReadOnce(): void {
		$source = new class implements WikiConfigSource {
			public int $reads = 0;

			public function getConfig(): ?array {
				$this->reads++;
				return [ 'general' => [ 'mapHeight' => 420 ] ];
			}
		};

		$settings = new EffectiveSettings( [ 'egMapsMapHeight' => 350 ], ConfigSchema::newDefault(), $source, true );

		$settings->get( 'egMapsMapHeight' );
		$settings->get( 'egMapsMapWidth' );

		$this->assertSame( 1, $source->reads );
	}

	public function testWikiSourceThrowableFallsBackToPhpSettings(): void {
		$source = new class implements WikiConfigSource {
			public function getConfig(): ?array {
				throw new RuntimeException( 'Database unavailable' );
			}
		};

		$settings = new EffectiveSettings( [ 'egMapsMapHeight' => 350 ], ConfigSchema::newDefault(), $source, true );

		$this->assertSame( 350, $settings->get( 'egMapsMapHeight' ) );
	}

}
