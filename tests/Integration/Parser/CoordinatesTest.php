<?php

namespace Maps\Tests\Integration\Parser;

use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CoordinatesTest extends TestCase {

	private function parse( string $textToParse ): string {
		$parser = new \Parser();

		return $parser->parse( $textToParse, \Title::newMainPage(), new \ParserOptions() )->getText();
	}

	public function testGivenInvalidCoordinates_errorIsShown() {
		$this->assertContains(
			'<span class="errorbox">',
			$this->parse( '{{#coordinates:nope}}' )
		);
	}

	public function testGivenNoCoordinates_errorIsShown() {
		$this->assertContains(
			'<span class="errorbox">',
			$this->parse( '{{#coordinates:}}' )
		);
	}

	public function testGivenValidCoordinates_theyAreFormatted() {
		$this->assertContains(
			'1° 0\' 0.00" N, 1° 0\' 0.00" E',
			$this->parse( '{{#coordinates:1,1}}' )
		);
	}

	public function testGivenFormat_coordinatesAreConvertedToIt() {
		$this->assertContains(
			'1.000000° N, 1.000000° E',
			$this->parse( '{{#coordinates:1,1|format=dd}}' )
		);
	}

	public function testGivenDirectionalParameter_itGetsUsed() {
		$this->assertContains(
			'1° 0\' 0.00", 1° 0\' 0.00"',
			$this->parse( '{{#coordinates:1,1|directional=no}}' )
		);
	}

	public function testCoordinatesInNonDms_theyGetParsed() {
		$this->assertContains(
			'1° 20\' 13.20" N, 4° 12\' 0.00" W',
			$this->parse( '{{#coordinates:1.337°, -4.2°}}' )
		);
	}

	public function testGivenInvalidFormat_defaultFormatGetsUsed() {
		$this->assertContains(
			'1° 0\' 0.00" N, 1° 0\' 0.00" E',
			$this->parse( '{{#coordinates:1,1|format=such}}' )
		);
	}

	public function testRoundingWhenFormattingAsFloat() {
		$this->assertContains(
			'52.136945 N, 0.466722 W',
			$this->parse( '{{#coordinates:52.136945,-0.466722|format=float}}' )
		);
	}

	public function testRoundingWhenFormattingAsDMS() {
		$this->assertContains(
			'52° 8\' 13.00" N, 0° 28\' 0.20" W',
			$this->parse( '{{#coordinates:52.136945,-0.466722|format=dms}}' )
		);
	}

	public function testRoundingWhenFormattingAsDD() {
		$this->assertContains(
			'52.136945° N, 0.466722° W',
			$this->parse( '{{#coordinates:52.136945,-0.466722|format=dd}}' )
		);
	}

	public function testRoundingWhenFormattingAsDM() {
		$this->assertContains(
			'52° 8.2167\' N, 0° 28.0033\' W',
			$this->parse( '{{#coordinates:52.136945,-0.466722|format=dm}}' )
		);
	}

}