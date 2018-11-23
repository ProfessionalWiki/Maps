<?php

namespace Maps\Tests\Integration\Parser;

use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DisplayMapTest extends TestCase {

	public function testMapIdIsSet() {
		$this->assertContains(
			'id="map_leaflet_',
			$this->parse( '{{#display_map:1,1|service=leaflet}}' )
		);
	}

	private function parse( string $textToParse ): string {
		$parser = new \Parser();

		return $parser->parse( $textToParse, \Title::newMainPage(), new \ParserOptions() )->getText();
	}

	public function testServiceSelectionWorks() {
		$this->assertContains(
			'maps-googlemaps3',
			$this->parse( '{{#display_map:1,1|service=google}}' )
		);
	}

	public function testSingleCoordinatesAreIncluded() {
		$this->assertContains(
			'"lat":1,"lon":1',
			$this->parse( '{{#display_map:1,1}}' )
		);
	}

	public function testMultipleCoordinatesAreIncluded() {
		$result = $this->parse( '{{#display_map:1,1; 4,2}}' );

		$this->assertContains( '"lat":1,"lon":1', $result );
		$this->assertContains( '"lat":4,"lon":2', $result );
	}

	public function testWhenValidZoomIsSpecified_itGetsUsed() {
		$this->assertContains(
			'"zoom":5',
			$this->parse( '{{#display_map:1,1|service=google|zoom=5}}' )
		);
	}

	public function testWhenZoomIsNotSpecifiedAndThereIsOnlyOneLocation_itIsDefaulted() {
		$this->assertContains(
			'"zoom":' . $GLOBALS['egMapsGMaps3Zoom'],
			$this->parse( '{{#display_map:1,1|service=google}}' )
		);
	}

	public function testWhenZoomIsNotSpecifiedAndThereAreMultipleLocations_itIsDefaulted() {
		$this->assertContains(
			'"zoom":false',
			$this->parse( '{{#display_map:1,1;2,2|service=google}}' )
		);
	}

	public function testWhenZoomIsInvalid_itIsDefaulted() {
		$this->assertContains(
			'"zoom":' . $GLOBALS['egMapsGMaps3Zoom'],
			$this->parse( '{{#display_map:1,1|service=google|zoom=tomato}}' )
		);
	}

	public function testTagIsRendered() {
		$this->assertContains(
			'"lat":1,"lon":1',
			$this->parse( '<display_map>1,1</display_map>' )
		);
	}

	public function testTagServiceParameterIsUsed() {
		$this->assertContains(
			'maps-googlemaps3',
			$this->parse( '<display_map service="google">1,1</display_map>' )
		);
	}

	public function testWhenThereAreNoLocations_locationsArrayIsEmpty() {
		$this->assertContains(
			'"locations":[]',
			$this->parse( '{{#display_map:}}' )
		);
	}

	public function testLocationTitleGetsIncluded() {
		$this->assertContains(
			'"title":"title',
			$this->parse( '{{#display_map:1,1~title}}' )
		);
	}

	public function testLocationDescriptionGetsIncluded() {
		$this->assertContains(
			'such description',
			$this->parse( '{{#display_map:1,1~title~such description}}' )
		);
	}

	public function testRectangleDisplay() {
		$this->assertContains(
			'"title":"title',
			$this->parse( '{{#display_map:rectangles=1,1:2,2~title}}' )
		);
	}

	public function testCircleDisplay() {
		$this->assertContains(
			'"title":"title',
			$this->parse( '{{#display_map:circles=1,1:2~title}}' )
		);
	}

	public function testRectangleFillOpacityIsUsed() {
		$this->assertContains(
			'"fillOpacity":"fill opacity"',
			$this->parse( '{{#display_map:rectangles=1,1:2,2~title~text~color~opacity~thickness~fill color~fill opacity}}' )
		);
	}

	public function testRectangleFillColorIsUsed() {
		$this->assertContains(
			'"fillColor":"fill color"',
			$this->parse( '{{#display_map:rectangles=1,1:2,2~title~text~color~opacity~thickness~fill color~fill opacity}}' )
		);
	}

	public function testServiceSelectionWorksWhenItIsPrecededByMultipleParameters() {
		$this->assertContains(
			'maps-googlemaps3',
			$this->parse(
				"{{#display_map:rectangles=\n  1,1:2,2~title~text~color\n| scrollwheelzoom=off\n| service = google}}"
			)
		);
	}

	// TODO: need DI to test
//	public function testWhenLocationHasVisitedIconModifier_itIsUsed() {
//		$this->assertContains(
//			'"visitedicon":"VisitedIcon.png"',
//			$this->parse( '{{#display_map:1,1~title~text~icon~group~inline label~VisitedIcon.png}}' )
//		);
//	}
//
//	public function testWhenLocationHasVisitedIconModifierWithNamespacePrefix_thePrefixGetsRemoved() {
//		$this->assertContains(MapsMapperTest
//			'"visitedicon":"VisitedIcon.png"',
//			$this->parse( '{{#display_map:1,1~title~text~icon~group~inline label~File:VisitedIcon.png}}' )
//		);
//	}
//
//	public function testWhenVisitedIconParameterIsProvidedWithNamespacePrefix_thePrefixGetsRemoved() {
//		$this->assertContains(
//			'"visitedicon":"VisitedIcon.png"',
//			$this->parse( '{{#display_map:1,1|visitedicon=File:VisitedIcon.png}}' )
//		);
//	}
//
//	public function testWhenLocationHasIconModifierWithNamespacePrefix_thePrefixGetsRemoved() {
//		$this->assertContains(
//			'"icon":"Icon.png"',
//			$this->parse( '{{#display_map:1,1~title~text~File:Icon.png}}' )
//		);
//	}

	public function testWhenIconParameterIsProvidedButEmpty_itIsDefaulted() {
		$this->assertContains(
			'"icon":"","inlineLabel":"Ghent',
			$this->parse(
				"{{#display_map:Gent, Belgie~The city Ghent~Ghent is awesome~ ~ ~Ghent}}"
			)
		);
	}

	public function testWhenLocationHasNoTitleAndText_textFieldIsEmptyString() {
		$this->assertContains(
			'"text":""',
			$this->parse( '{{#display_map:1,1}}' )
		);
	}

}