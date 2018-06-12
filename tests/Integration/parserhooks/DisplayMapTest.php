<?php

namespace Maps\Test;

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

}