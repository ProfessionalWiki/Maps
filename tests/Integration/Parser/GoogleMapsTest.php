<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;

class GoogleMapsTest extends TestCase {

	private function parse( string $textToParse ): string {
		$parser = MediaWikiServices::getInstance()->getParser();

		return $parser->parse( $textToParse, \Title::newMainPage(), new \ParserOptions() )->getText();
	}

	public function testGoogleMapsKmlFiltersInvalidFileNames() {
		$this->assertStringContainsString(
			'"kml":["ValidFile.kml"],',
			$this->parse(
				"{{#google_maps:kml=, ,ValidFile.kml ,}}"
			)
		);
	}

	public function testWhenValidZoomIsSpecified_itGetsUsed() {
		$this->assertStringContainsString(
			'"zoom":5',
			$this->parse( '{{#google_maps:1,1|zoom=5}}' )
		);
	}

	public function testWhenZoomIsNotSpecifiedAndThereIsOnlyOneLocation_itIsDefaulted() {
		$this->assertStringContainsString(
			'"zoom":' . $GLOBALS['egMapsGMaps3Zoom'],
			$this->parse( '{{#google_maps:1,1}}' )
		);
	}

	public function testWhenZoomIsNotSpecifiedAndThereAreMultipleLocations_itIsDefaulted() {
		$this->assertStringContainsString(
			'"zoom":false',
			$this->parse( '{{#google_maps:1,1;2,2}}' )
		);
	}

	public function testWhenZoomIsInvalid_itIsDefaulted() {
		$this->assertStringContainsString(
			'"zoom":' . $GLOBALS['egMapsGMaps3Zoom'],
			$this->parse( '{{#google_maps:1,1|zoom=tomato}}' )
		);
	}

}
