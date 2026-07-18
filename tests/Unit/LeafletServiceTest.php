<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\LeafletLayerDefinitions;
use Maps\LeafletService;
use Maps\Map\MapData;
use Maps\Tests\TestDoubles\ImageValueObject;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \Maps\LeafletService
 */
class LeafletServiceTest extends TestCase {

	private array $originalGlobals = [];

	protected function setUp(): void {
		parent::setUp();

		$this->originalGlobals = [
			'egMapsLeafletAvailableLayers' => $GLOBALS['egMapsLeafletAvailableLayers'] ?? null,
			'egMapsLeafletAvailableOverlayLayers' => $GLOBALS['egMapsLeafletAvailableOverlayLayers'] ?? null,
		];

		$GLOBALS['egMapsLeafletAvailableLayers'] = [ 'OpenStreetMap' => true, 'OpenTopoMap' => true ];
		$GLOBALS['egMapsLeafletAvailableOverlayLayers'] = [ 'OpenSeaMap' => true, 'OpenRailwayMap' => true ];
	}

	protected function tearDown(): void {
		$GLOBALS['egMapsLeafletAvailableLayers'] = $this->originalGlobals['egMapsLeafletAvailableLayers'];
		$GLOBALS['egMapsLeafletAvailableOverlayLayers'] = $this->originalGlobals['egMapsLeafletAvailableOverlayLayers'];

		parent::tearDown();
	}

	public function testZeroWidthImageLayerIsSkipped() {
		$imageRepo = new InMemoryImageRepository();
		$imageRepo->addImage( 'zero.png', new ImageValueObject( 'http://example.com/zero.png', 0, 100 ) );

		$result = $this->getJsImageLayers( $imageRepo, [ 'zero.png' ] );

		$this->assertSame( [], $result );
	}

	public function testValidImageLayerIsIncluded() {
		$imageRepo = new InMemoryImageRepository();
		$imageRepo->addImage( 'valid.png', new ImageValueObject( 'http://example.com/valid.png', 200, 100 ) );

		$result = $this->getJsImageLayers( $imageRepo, [ 'valid.png' ] );

		$this->assertCount( 1, $result );
		$this->assertSame( 'valid.png', $result[0]['name'] );
		$this->assertSame( 'http://example.com/valid.png', $result[0]['url'] );
		$this->assertSame( 100, $result[0]['width'] );
		$this->assertSame( 50.0, $result[0]['height'] );
	}

	public function testNonExistentImageLayerIsSkipped() {
		$imageRepo = new InMemoryImageRepository();

		$result = $this->getJsImageLayers( $imageRepo, [ 'missing.png' ] );

		$this->assertSame( [], $result );
	}

	public function testNonWhitelistedOverlaysAreRemoved() {
		$mapData = $this->newLeafletMapData( [
			'overlays' => [ 'OpenSeaMap', '<img src=x onerror="alert(1)">', 'OpenRailwayMap' ]
		] );

		$this->assertSame(
			[ 'OpenSeaMap', 'OpenRailwayMap' ],
			$mapData->getParameters()['overlays']
		);
	}

	public function testNonWhitelistedLayersAreRemoved() {
		$mapData = $this->newLeafletMapData( [
			'layers' => [ 'OpenStreetMap', '<img src=x onerror="alert(1)">', 'OpenTopoMap' ]
		] );

		$this->assertSame(
			[ 'OpenStreetMap', 'OpenTopoMap' ],
			$mapData->getParameters()['layers']
		);
	}

	public function testCustomLayerDefinitionSurvivesFiltering() {
		$mapData = $this->newLeafletMapData(
			[ 'layers' => [ 'OpenStreetMap', 'Historic', '<img src=x onerror="alert(1)">' ] ],
			new LeafletLayerDefinitions( [
				'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
			] )
		);

		$this->assertSame(
			[ 'OpenStreetMap', 'Historic' ],
			$mapData->getParameters()['layers']
		);
	}

	public function testCustomOverlayDefinitionSurvivesFiltering() {
		$mapData = $this->newLeafletMapData(
			[ 'overlays' => [ 'OpenSeaMap', 'Historic', '<img src=x onerror="alert(1)">' ] ],
			new LeafletLayerDefinitions( [
				'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
			] )
		);

		$this->assertSame(
			[ 'OpenSeaMap', 'Historic' ],
			$mapData->getParameters()['overlays']
		);
	}

	public function testUsedLayerDefinitionIsSerializedIntoMapData() {
		$mapData = $this->newLeafletMapData(
			[ 'layers' => [ 'OpenStreetMap', 'Historic' ] ],
			new LeafletLayerDefinitions( [
				'Historic' => [
					'url' => 'https://tiles.example/{z}/{x}/{y}.png',
					'options' => [ 'attribution' => 'Example' ],
				],
			] )
		);

		$this->assertSame(
			[
				'Historic' => [
					'url' => 'https://tiles.example/{z}/{x}/{y}.png',
					'options' => [ 'attribution' => 'Example' ],
					'wms' => false,
				],
			],
			$mapData->getParameters()['layerDefinitions']
		);
	}

	public function testDefinitionUsedAsOverlayIsSerializedIntoMapData() {
		$mapData = $this->newLeafletMapData(
			[ 'overlays' => [ 'OpenSeaMap', 'Historic' ] ],
			new LeafletLayerDefinitions( [
				'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
			] )
		);

		$this->assertArrayHasKey( 'Historic', $mapData->getParameters()['layerDefinitions'] );
	}

	public function testOnlyUsedLayerDefinitionsAreSerialized() {
		$mapData = $this->newLeafletMapData(
			[ 'layers' => [ 'Used' ] ],
			new LeafletLayerDefinitions( [
				'Unused' => [ 'url' => 'https://tiles.example/unused/{z}/{x}/{y}.png' ],
				'Used' => [ 'url' => 'https://tiles.example/used/{z}/{x}/{y}.png' ],
				'AlsoUnused' => [ 'url' => 'https://tiles.example/also/{z}/{x}/{y}.png' ],
			] )
		);

		$this->assertSame(
			[ 'Used' ],
			array_keys( $mapData->getParameters()['layerDefinitions'] )
		);
	}

	public function testLayerDefinitionsKeyIsAbsentWhenNoneAreConfigured() {
		$mapData = $this->newLeafletMapData( [ 'layers' => [ 'OpenStreetMap' ] ] );

		$this->assertArrayNotHasKey( 'layerDefinitions', $mapData->getParameters() );
	}

	public function testLayerDefinitionsKeyIsAbsentWhenConfiguredDefinitionsAreUnused() {
		$mapData = $this->newLeafletMapData(
			[ 'layers' => [ 'OpenStreetMap' ] ],
			new LeafletLayerDefinitions( [
				'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
			] )
		);

		$this->assertArrayNotHasKey( 'layerDefinitions', $mapData->getParameters() );
	}

	public function testDefinitionShadowingStockLayerIsSerialized() {
		$mapData = $this->newLeafletMapData(
			[ 'layers' => [ 'OpenStreetMap' ] ],
			new LeafletLayerDefinitions( [
				'OpenSeaMap' => [ 'url' => 'https://tiles.example/before/{z}/{x}/{y}.png' ],
				'OpenStreetMap' => [ 'url' => 'https://tiles.example/shadow/{z}/{x}/{y}.png' ],
				'OpenTopoMap' => [ 'url' => 'https://tiles.example/after/{z}/{x}/{y}.png' ],
			] )
		);

		$this->assertSame(
			[ 'OpenStreetMap' ],
			array_keys( $mapData->getParameters()['layerDefinitions'] )
		);
		$this->assertSame(
			'https://tiles.example/shadow/{z}/{x}/{y}.png',
			$mapData->getParameters()['layerDefinitions']['OpenStreetMap']['url']
		);
	}

	private function newLeafletMapData( array $overrides, ?LeafletLayerDefinitions $layerDefinitions = null ): MapData {
		$params = array_merge(
			[
				'geojson' => '',
				'image layers' => [],
				'layers' => [],
				'overlays' => [],
			],
			$overrides
		);

		return $this->newService( new InMemoryImageRepository(), $layerDefinitions )
			->newMapDataFromParameters( $params );
	}

	private function newService(
		InMemoryImageRepository $imageRepo,
		?LeafletLayerDefinitions $layerDefinitions = null
	): LeafletService {
		return new LeafletService(
			$imageRepo,
			$layerDefinitions ?? new LeafletLayerDefinitions( [] )
		);
	}

	private function getJsImageLayers( InMemoryImageRepository $imageRepo, array $imageLayers ): array {
		$service = $this->newService( $imageRepo );

		$method = new ReflectionMethod( LeafletService::class, 'getJsImageLayers' );
		$method->setAccessible( true );

		return $method->invoke( $service, $imageLayers );
	}

}
