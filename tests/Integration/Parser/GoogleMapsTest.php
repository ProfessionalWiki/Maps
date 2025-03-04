<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

class GoogleMapsTest extends TestCase {

	private function assertStringContainsData( string $expected, string $html ): void {
		$this->assertStringContainsString( htmlspecialchars( $expected ), $html );
	}

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testGoogleMapsKmlFiltersInvalidFileNames() {
		$this->assertStringContainsData(
			'"kml":["ValidFile.kml"],',
			$this->parse(
				"{{#google_maps:kml=, ,ValidFile.kml ,}}"
			)
		);
	}

	public function testWhenValidZoomIsSpecified_itGetsUsed() {
		$this->assertStringContainsData(
			'"zoom":5',
			$this->parse( '{{#google_maps:1,1|zoom=5}}' )
		);
	}

	public function testWhenZoomIsNotSpecifiedAndThereIsOnlyOneLocation_itIsDefaulted() {
		$this->assertStringContainsData(
			'"zoom":' . $GLOBALS['egMapsGMaps3Zoom'],
			$this->parse( '{{#google_maps:1,1}}' )
		);
	}

	public function testWhenZoomIsNotSpecifiedAndThereAreMultipleLocations_itIsDefaulted() {
		$this->assertStringContainsData(
			'"zoom":false',
			$this->parse( '{{#google_maps:1,1;2,2}}' )
		);
	}

	public function testWhenZoomIsInvalid_itIsDefaulted() {
		$this->assertStringContainsData(
			'"zoom":' . $GLOBALS['egMapsGMaps3Zoom'],
			$this->parse( '{{#google_maps:1,1|zoom=tomato}}' )
		);
	}

	public function testInvalidMapTypesGetLeftOut() {
		$this->assertStringContainsData(
			'"types":["ROADMAP"]',
			$this->parse( '{{#google_maps:1,1|types=normal, foobar}}' )
		);
	}

}
