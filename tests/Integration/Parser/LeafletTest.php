<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\LeafletLayerDefinitions;
use Maps\LeafletService;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\TestDoubles\ImageValueObject;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

class LeafletTest extends TestCase {

	private array $originalLayerDefinitions;

	protected function setUp(): void {
		parent::setUp();

		$this->originalLayerDefinitions = $GLOBALS['egMapsLeafletLayerDefinitions'] ?? [];
		$this->setLayerDefinitions( [] );
	}

	protected function tearDown(): void {
		$GLOBALS['egMapsLeafletLayerDefinitions'] = $this->originalLayerDefinitions;

		parent::tearDown();
	}

	private function setLayerDefinitions( array $definitions ): void {
		$GLOBALS['egMapsLeafletLayerDefinitions'] = $definitions;
		MapsTestFactory::newTestInstance();
	}

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	private function assertStringContainsData( string $expected, string $html ): void {
		$this->assertStringContainsString( htmlspecialchars( $expected ), $html );
	}

	public function testLeafletImageLayersIgnoresNotFoundImages() {
		$this->assertStringContainsData(
			'"imageLayers":[]',
			$this->parse(
				"{{#leaflet:image layers=404.png}}"
			)
		);
	}

	public function testLeafletImageLayersIgnoresImageUrls() {
		$this->assertStringContainsData(
			'"imageLayers":[]',
			$this->parse(
				"{{#leaflet:image layers=https://user-images.githubusercontent.com/62098559/76514021-3fa9be80-647d-11ea-82ae-715420a5c432.png}}"
			)
		);
	}

	public function testLeafletImageLayer() {
		$factory = MapsTestFactory::newTestInstance();

		$factory->imageRepo->addImage(
			'MyImage.png',
			new ImageValueObject( '/tmp/example/image.png', 40, 20 )
		);

		$html = $this->parse( "{{#leaflet:image layers=MyImage.png}}" );

		$this->assertStringContainsData( '"name":"MyImage.png"', $html );
		$this->assertStringContainsData( '"url":"/tmp/example/image.png"', $html );
		$this->assertStringContainsData( '"width":100', $html );
		$this->assertStringContainsData( '"height":50', $html );
	}

	public function testCustomLayerNameIsAValidLayerValue() {
		$service = new LeafletService(
			new InMemoryImageRepository(),
			new LeafletLayerDefinitions( [ 'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ] ] )
		);

		$values = $service->getParameterInfo()['layers']['values'];

		$this->assertContains( 'Historic', $values );
		$this->assertContains( 'OpenStreetMap', $values );
	}

	public function testCustomLayerNameIsAValidOverlayValue() {
		$service = new LeafletService(
			new InMemoryImageRepository(),
			new LeafletLayerDefinitions( [ 'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ] ] )
		);

		$values = $service->getParameterInfo()['overlays']['values'];

		$this->assertContains( 'Historic', $values );
		$this->assertContains( 'OpenSeaMap', $values );
	}

	public function testUsedCustomLayerDefinitionIsSerializedIntoMapData() {
		$this->setLayerDefinitions( [
			'Historic' => [
				'url' => 'https://tiles.example/historic/{z}/{x}/{y}.png',
				'options' => [ 'attribution' => 'Historic tiles' ],
			],
		] );

		$html = $this->parse( '{{#leaflet:layers=Historic}}' );

		$this->assertStringContainsData( '"layerDefinitions":', $html );
		$this->assertStringContainsData( '"url":"https://tiles.example/historic/{z}/{x}/{y}.png"', $html );
		$this->assertStringContainsData( '"attribution":"Historic tiles"', $html );
		$this->assertStringContainsData( '"wms":false', $html );
	}

	public function testMapWithoutCustomLayersHasNoLayerDefinitions() {
		$html = $this->parse( '{{#leaflet:}}' );

		$this->assertStringNotContainsString( 'layerDefinitions', $html );
	}

}
