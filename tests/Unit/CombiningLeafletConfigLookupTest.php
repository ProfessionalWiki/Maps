<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\CombiningLeafletConfigLookup;
use Maps\Tests\TestDoubles\StubLeafletConfigSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\CombiningLeafletConfigLookup
 */
class CombiningLeafletConfigLookupTest extends TestCase {

	private function phpConfig( array $overrides = [] ): array {
		return array_merge(
			[
				'layerDefinitions' => [],
				'defaultLayers' => [ 'OpenStreetMap' ],
				'defaultOverlays' => [],
				'availableLayers' => [ 'OpenStreetMap' => true ],
				'availableOverlays' => [ 'OpenSeaMap' => true ],
			],
			$overrides
		);
	}

	private function newLookup( array $phpConfig, ?array $wikiConfig, bool $enabled = true ): CombiningLeafletConfigLookup {
		return new CombiningLeafletConfigLookup(
			$phpConfig,
			new StubLeafletConfigSource( $wikiConfig ),
			$enabled
		);
	}

	public function testUsesPhpConfigWhenThereIsNoWikiConfig() {
		$config = $this->newLookup(
			$this->phpConfig( [ 'defaultLayers' => [ 'OpenTopoMap' ] ] ),
			null
		)->getConfig();

		$this->assertSame( [ 'OpenTopoMap' ], $config->getDefaultLayers() );
	}

	public function testWikiLayerDefinitionsAreMergedWithPhpDefinitions() {
		$config = $this->newLookup(
			$this->phpConfig( [
				'layerDefinitions' => [ 'Php' => [ 'url' => 'https://tiles.example/php/{z}/{x}/{y}.png' ] ],
			] ),
			[ 'layerDefinitions' => [ 'Wiki' => [ 'url' => 'https://tiles.example/wiki/{z}/{x}/{y}.png' ] ] ]
		)->getConfig();

		$names = $config->getLayerDefinitions()->getLayerNames();

		$this->assertContains( 'Php', $names );
		$this->assertContains( 'Wiki', $names );
	}

	public function testWikiLayerDefinitionWinsOnNameCollision() {
		$config = $this->newLookup(
			$this->phpConfig( [
				'layerDefinitions' => [ 'Shared' => [ 'url' => 'https://tiles.example/php/{z}/{x}/{y}.png' ] ],
			] ),
			[ 'layerDefinitions' => [ 'Shared' => [ 'url' => 'https://tiles.example/wiki/{z}/{x}/{y}.png' ] ] ]
		)->getConfig();

		$this->assertSame(
			'https://tiles.example/wiki/{z}/{x}/{y}.png',
			$config->getLayerDefinitions()->getDefinitions( [ 'Shared' ] )['Shared']['url']
		);
	}

	public function testAvailableLayersAreMergedPerNameWithWikiWinning() {
		$config = $this->newLookup(
			$this->phpConfig( [ 'availableLayers' => [ 'Kept' => true, 'Disabled' => true ] ] ),
			[ 'availableLayers' => [ 'Disabled' => false, 'Added' => true ] ]
		)->getConfig();

		$available = $config->getAvailableLayers();

		$this->assertTrue( $available['Kept'] );
		$this->assertFalse( $available['Disabled'] );
		$this->assertTrue( $available['Added'] );
	}

	public function testWikiDefaultLayersReplacePhpDefaultLayers() {
		$config = $this->newLookup(
			$this->phpConfig( [ 'defaultLayers' => [ 'OpenStreetMap' ] ] ),
			[ 'defaultLayers' => [ 'OpenTopoMap', 'Esri.WorldImagery' ] ]
		)->getConfig();

		$this->assertSame( [ 'OpenTopoMap', 'Esri.WorldImagery' ], $config->getDefaultLayers() );
	}

	public function testPhpDefaultLayersAreKeptWhenWikiConfigOmitsThem() {
		$config = $this->newLookup(
			$this->phpConfig( [ 'defaultLayers' => [ 'OpenStreetMap' ] ] ),
			[ 'layerDefinitions' => [] ]
		)->getConfig();

		$this->assertSame( [ 'OpenStreetMap' ], $config->getDefaultLayers() );
	}

	public function testInvalidWikiDefaultLayersFallBackToPhp() {
		$config = $this->newLookup(
			$this->phpConfig( [ 'defaultLayers' => [ 'OpenStreetMap' ] ] ),
			[ 'defaultLayers' => 'not-a-list' ]
		)->getConfig();

		$this->assertSame( [ 'OpenStreetMap' ], $config->getDefaultLayers() );
	}

	public function testWikiConfigIsIgnoredWhenDisabled() {
		$config = $this->newLookup(
			$this->phpConfig( [ 'defaultLayers' => [ 'OpenStreetMap' ] ] ),
			[ 'defaultLayers' => [ 'OpenTopoMap' ] ],
			false
		)->getConfig();

		$this->assertSame( [ 'OpenStreetMap' ], $config->getDefaultLayers() );
	}

	public function testNumericWikiLayerNameIsPreserved() {
		$config = $this->newLookup(
			$this->phpConfig(),
			[ 'layerDefinitions' => [ '1904' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ] ] ]
		)->getConfig();

		$this->assertArrayHasKey(
			'1904',
			$config->getLayerDefinitions()->getDefinitions( [ '1904' ] )
		);
	}

	public function testConfigIsMemoized() {
		$lookup = $this->newLookup( $this->phpConfig(), null );

		$this->assertSame( $lookup->getConfig(), $lookup->getConfig() );
	}

}
