<?php

namespace Maps\Tests\Integration\parsers;

use Maps\Presentation\WikitextParsers\WmsOverlayParser;
use PHPUnit\Framework\TestCase;
use PHPUnit4And6Compat;
use ValueParsers\ParseException;

/**
 * @covers \Maps\Presentation\WikitextParsers\WmsOverlayParser
 * @licence GNU GPL v2+
 * @author Mathias Mølster Lidal <mathiaslidal@gmail.com>
 */
class WmsOverlayParserTest extends TestCase {
	use PHPUnit4And6Compat;

	public function testGivenValidInput_parserReturnsOverlayObject() {
		$parser = new WmsOverlayParser();

		$overlay = $parser->parse( 'http://demo.cubewerx.com/demo/cubeserv/cubeserv.cgi? Foundation.GTOPO30' );

		$this->assertSame(
			'http://demo.cubewerx.com/demo/cubeserv/cubeserv.cgi?',
			$overlay->getWmsServerUrl()
		);

		$this->assertSame(
			'Foundation.GTOPO30',
			$overlay->getWmsLayerName()
		);
	}

	public function testWhenStyleNameIsSpecified_getStyleNameReturnsIt() {
		$parser = new WmsOverlayParser();

		$overlay = $parser->parse(
			'http://maps.imr.no:80/geoserver/wms? vulnerable_areas:Identified_coral_area coral_identified_areas'
		);

		$this->assertSame(
			'coral_identified_areas',
			$overlay->getWmsStyleName()
		);
	}

	public function testWhenThereAreLessThanTwoSegments_parseExceptionIsThrown() {
		$parser = new WmsOverlayParser();

		$this->expectException( ParseException::class );
		$parser->parse( 'Such' );
	}

}
