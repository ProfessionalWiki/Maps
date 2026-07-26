<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Config\ConfigSchema;
use Maps\Config\EffectiveSettings;
use Maps\GeoJsonPages\GeoJsonContent;
use Maps\LeafletService;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\TestDoubles\ImageValueObject;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use Maps\Tests\TestDoubles\StubWikiConfigSource;
use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\LeafletService
 */
class LeafletTest extends TestCase {

	private const FIRST_FEATURE = 'Feature of the first page';
	private const SECOND_FEATURE = 'Feature of the second page';

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

	private function newServiceWithCustomLayer(): LeafletService {
		return new LeafletService(
			new InMemoryImageRepository(),
			new EffectiveSettings(
				[
					'egMapsLeafletLayerDefinitions' => [ 'Historic' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ] ],
					'egMapsLeafletLayers' => [ 'OpenStreetMap' ],
					'egMapsLeafletOverlayLayers' => [],
					'egMapsLeafletAvailableLayers' => [ 'OpenStreetMap' => true ],
					'egMapsLeafletAvailableOverlayLayers' => [ 'OpenSeaMap' => true ],
					'egMapsLeafletZoom' => 14,
					'egMapsResizableByDefault' => false,
				],
				ConfigSchema::newDefault(),
				new StubWikiConfigSource( null ),
				true
			)
		);
	}

	public function testCustomLayerNameIsAValidLayerValue() {
		$values = $this->newServiceWithCustomLayer()->getParameterInfo()['layers']['values'];

		$this->assertContains( 'Historic', $values );
		$this->assertContains( 'OpenStreetMap', $values );
	}

	public function testCustomLayerNameIsAValidOverlayValue() {
		$values = $this->newServiceWithCustomLayer()->getParameterInfo()['overlays']['values'];

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

	private function createGeoJsonPage( string $pageName, string $featureTitle ): void {
		PageCreator::instance()->createPageWithContent(
			'GeoJson:' . $pageName,
			new GeoJsonContent( json_encode( [
				'type' => 'FeatureCollection',
				'features' => [
					[
						'type' => 'Feature',
						'geometry' => [ 'type' => 'Point', 'coordinates' => [ 4.35, 50.85 ] ],
						'properties' => [ 'title' => $featureTitle ],
					],
				],
			] ) )
		);
	}

	private function createTwoGeoJsonPages(): void {
		$this->createGeoJsonPage( 'FirstSource', self::FIRST_FEATURE );
		$this->createGeoJsonPage( 'SecondSource', self::SECOND_FEATURE );
	}

	private function assertBothFeaturesAreRendered( string $html ): void {
		$this->assertStringContainsData( self::FIRST_FEATURE, $html );
		$this->assertStringContainsData( self::SECOND_FEATURE, $html );
	}

	public function testBothGeoJsonSourcesAreRendered() {
		$this->createTwoGeoJsonPages();

		$this->assertBothFeaturesAreRendered(
			$this->parse( '{{#leaflet:geojson=FirstSource;SecondSource}}' )
		);
	}

	public function testWhitespaceAfterTheDelimiterIsIgnored() {
		$this->createTwoGeoJsonPages();

		$this->assertBothFeaturesAreRendered(
			$this->parse( '{{#leaflet:geojson=FirstSource; SecondSource}}' )
		);
	}

	public function testMissingSourceDoesNotStopTheRemainingOneFromRendering() {
		$this->createGeoJsonPage( 'SecondSource', self::SECOND_FEATURE );

		$html = $this->parse( '{{#leaflet:geojson=NoSuchPage;SecondSource}}' );

		$this->assertStringContainsData( '"geojson":[{"type":"FeatureCollection"', $html );
		$this->assertStringContainsData( self::SECOND_FEATURE, $html );
	}

	public function testGeoJsonSourceIsNullWithMultipleSources() {
		$this->createTwoGeoJsonPages();

		$this->assertStringContainsData(
			'"GeoJsonSource":null',
			$this->parse( '{{#leaflet:geojson=FirstSource;SecondSource}}' )
		);
	}

	public function testGeoJsonSourceIsThePageNameWithASingleSource() {
		$this->createGeoJsonPage( 'FirstSource', self::FIRST_FEATURE );

		$this->assertStringContainsData(
			'"GeoJsonSource":"FirstSource"',
			$this->parse( '{{#leaflet:geojson=FirstSource}}' )
		);
	}

	public function testTrailingDelimiterLeavesTheSingleSourceEditable() {
		$this->createGeoJsonPage( 'FirstSource', self::FIRST_FEATURE );

		$this->assertStringContainsData(
			'"GeoJsonSource":"FirstSource"',
			$this->parse( '{{#leaflet:geojson=FirstSource;}}' )
		);
	}

}
