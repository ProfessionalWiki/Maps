<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

class GoogleMapsTest extends TestCase {

	private bool $originalAllowExternalDataFiles;

	protected function setUp(): void {
		parent::setUp();

		$this->originalAllowExternalDataFiles = $GLOBALS['egMapsAllowExternalDataFiles'] ?? false;
	}

	protected function tearDown(): void {
		$this->setAllowExternalDataFiles( $this->originalAllowExternalDataFiles );

		parent::tearDown();
	}

	private function setAllowExternalDataFiles( bool $allow ): void {
		$GLOBALS['egMapsAllowExternalDataFiles'] = $allow;
		MapsTestFactory::newTestInstance();
	}

	private function assertStringContainsData( string $expected, string $html ): void {
		$this->assertStringContainsString( htmlspecialchars( $expected ), $html );
	}

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testEmptyKmlEntriesAreDropped() {
		$this->setAllowExternalDataFiles( true );

		$this->assertStringContainsData(
			'"kml":["https://example.com/points.kml"],',
			$this->parse(
				"{{#google_maps:kml=, ,https://example.com/points.kml ,}}"
			)
		);
	}

	public function testKmlOnOtherHostsIsDroppedWhenExternalDataFilesAreNotAllowed() {
		$this->setAllowExternalDataFiles( false );

		$this->assertStringContainsData(
			'"kml":[]',
			$this->parse( '{{#google_maps:kml=https://example.com/points.kml}}' )
		);
	}

	public function testMapDataSaysExternalDataFilesAreAllowed() {
		$this->setAllowExternalDataFiles( true );

		$this->assertStringContainsData(
			'"allowexternaldatafiles":true',
			$this->parse( '{{#google_maps:1,1}}' )
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
