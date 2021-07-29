<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\GeoJsonPages\GeoJsonContent;
use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DisplayMapTest extends TestCase {

	private $originalHeight;
	private $originalWidth;

	public function setUp(): void {
		$this->originalHeight = $GLOBALS['egMapsMapHeight'];
		$this->originalWidth = $GLOBALS['egMapsMapWidth'];
	}

	public function tearDown(): void {
		$GLOBALS['egMapsMapHeight'] = $this->originalHeight;
		$GLOBALS['egMapsMapWidth'] = $this->originalWidth;
	}

	public function testMapIdIsSet() {
		$this->assertStringContainsString(
			'id="map_leaflet_',
			$this->parse( '{{#display_map:1,1|service=leaflet}}' )
		);
	}

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testServiceSelectionWorks() {
		$this->assertStringContainsString(
			'maps-googlemaps3',
			$this->parse( '{{#display_map:1,1|service=google}}' )
		);
	}

	public function testSingleCoordinatesAreIncluded() {
		$this->assertStringContainsString(
			'"lat":1,"lon":1',
			$this->parse( '{{#display_map:1,1}}' )
		);
	}

	public function testMultipleCoordinatesAreIncluded() {
		$result = $this->parse( '{{#display_map:1,1; 4,2}}' );

		$this->assertStringContainsString( '"lat":1,"lon":1', $result );
		$this->assertStringContainsString( '"lat":4,"lon":2', $result );
	}

	public function testTagIsRendered() {
		$this->assertStringContainsString(
			'"lat":1,"lon":1',
			$this->parse( '<display_map>1,1</display_map>' )
		);
	}

	public function testTagServiceParameterIsUsed() {
		$this->assertStringContainsString(
			'maps-googlemaps3',
			$this->parse( '<display_map service="google">1,1</display_map>' )
		);
	}

	public function testWhenThereAreNoLocations_locationsArrayIsEmpty() {
		$this->assertStringContainsString(
			'"locations":[]',
			$this->parse( '{{#display_map:}}' )
		);
	}

	public function testLocationTitleGetsIncluded() {
		$this->assertStringContainsString(
			'"title":"title',
			$this->parse( '{{#display_map:1,1~title}}' )
		);
	}

	public function testLocationDescriptionGetsIncluded() {
		$this->assertStringContainsString(
			'such description',
			$this->parse( '{{#display_map:1,1~title~such description}}' )
		);
	}

	public function testRectangleDisplay() {
		$this->assertStringContainsString(
			'"title":"title',
			$this->parse( '{{#display_map:rectangles=1,1:2,2~title}}' )
		);
	}

	public function testCircleDisplay() {
		$this->assertStringContainsString(
			'"title":"title',
			$this->parse( '{{#display_map:circles=1,1:2~title}}' )
		);
	}

	public function testRectangleFillOpacityIsUsed() {
		$this->assertStringContainsString(
			'"fillOpacity":"fill opacity"',
			$this->parse( '{{#display_map:rectangles=1,1:2,2~title~text~color~opacity~thickness~fill color~fill opacity}}' )
		);
	}

	public function testRectangleFillColorIsUsed() {
		$this->assertStringContainsString(
			'"fillColor":"fill color"',
			$this->parse( '{{#display_map:rectangles=1,1:2,2~title~text~color~opacity~thickness~fill color~fill opacity}}' )
		);
	}

	public function testServiceSelectionWorksWhenItIsPrecededByMultipleParameters() {
		$this->assertStringContainsString(
			'maps-googlemaps3',
			$this->parse(
				"{{#display_map:rectangles=\n  1,1:2,2~title~text~color\n| scrollwheelzoom=off\n| service = google}}"
			)
		);
	}

	public function testDimensionDefaultsAsInteger() {
		$GLOBALS['egMapsMapHeight'] = 420;
		$GLOBALS['egMapsMapWidth'] = 230;

		$this->assertStringContainsString(
			'height: 420px;',
			$this->parse( '{{#display_map:1,1}}' )
		);

		$this->assertStringContainsString(
			'width: 230px;',
			$this->parse( '{{#display_map:1,1}}' )
		);
	}

	// TODO: need DI to test
//	public function testWhenLocationHasVisitedIconModifier_itIsUsed() {
//		$this->assertStringContainsString(
//			'"visitedicon":"VisitedIcon.png"',
//			$this->parse( '{{#display_map:1,1~title~text~icon~group~inline label~VisitedIcon.png}}' )
//		);
//	}
//
//	public function testWhenLocationHasVisitedIconModifierWithNamespacePrefix_thePrefixGetsRemoved() {
//		$this->assertStringContainsString(MapsMapperTest
//			'"visitedicon":"VisitedIcon.png"',
//			$this->parse( '{{#display_map:1,1~title~text~icon~group~inline label~File:VisitedIcon.png}}' )
//		);
//	}
//
//	public function testWhenVisitedIconParameterIsProvidedWithNamespacePrefix_thePrefixGetsRemoved() {
//		$this->assertStringContainsString(
//			'"visitedicon":"VisitedIcon.png"',
//			$this->parse( '{{#display_map:1,1|visitedicon=File:VisitedIcon.png}}' )
//		);
//	}
//
//	public function testWhenLocationHasIconModifierWithNamespacePrefix_thePrefixGetsRemoved() {
//		$this->assertStringContainsString(
//			'"icon":"Icon.png"',
//			$this->parse( '{{#display_map:1,1~title~text~File:Icon.png}}' )
//		);
//	}

	public function testWhenIconParameterIsProvidedButEmpty_itIsDefaulted() {
		$this->assertStringContainsString(
			'"icon":"","inlineLabel":"Ghent',
			$this->parse(
				"{{#display_map:Gent, Belgie~The city Ghent~Ghent is awesome~ ~ ~Ghent}}"
			)
		);
	}

	public function testWhenLocationHasNoTitleAndText_textFieldIsEmptyString() {
		$this->assertStringContainsString(
			'"text":""',
			$this->parse( '{{#display_map:1,1}}' )
		);
	}

	public function testGeoJsonSourceForFile() {
		$this->assertStringContainsString(
			'"GeoJsonSource":null,',
			$this->parse(
				"{{#display_map:geojson=404}}"
			)
		);
	}

	public function testGeoJsonSourceForPage() {
		PageCreator::instance()->createPageWithContent(
			'GeoJson:TestPageSource',
			new GeoJsonContent( json_encode( [
				'type' => 'FeatureCollection',
				'features' => []
			] ) )
		);

		$this->assertStringContainsString(
			'"GeoJsonSource":"TestPageSource",',
			$this->parse(
				"{{#display_map:geojson=TestPageSource}}"
			)
		);
	}

}
