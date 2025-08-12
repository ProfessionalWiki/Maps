<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoders\Decorators\CoordinateFriendlyGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\NullGeocoder;
use Maps\LegacyModel\Circle;
use Maps\WikitextParsers\CircleParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\WikitextParsers\CircleParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParserTest extends TestCase {

	public function testGivenCoordinateAndRadius_parserReturnsCircle() {
		$circle = $this->newCircleParser()->parse( '57.421,23.90625:32684.605182' );

		$expectedLatLong = new LatLongValue( 57.421, 23.90625 );
		$this->assertTrue( $expectedLatLong->equals( $circle->getCircleCentre() ) );

		$this->assertSame( 32684.605182, $circle->getCircleRadius() );
	}

	private function newCircleParser(): CircleParser {
		return new CircleParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );
	}

	public function testGivenTitleAndText_circleHasProvidedMetaData() {
		$circle = $this->newCircleParser()->parse( '57.421,23.90625:32684.605182~title~text' );

		$this->assertSame( 'title', $circle->getTitle() );
		$this->assertSame( 'text', $circle->getText() );
	}

	public function testGivenNoRadius_radiusIsOne() {
		$circle = $this->newCircleParser()->parse( '42,42' );

		$this->assertSame( 1.0, $circle->getCircleRadius() );
	}

	public function testGivenNegative_radiusIsOne() {
		$circle = $this->newCircleParser()->parse( '42,42:-5' );

		$this->assertSame( 1.0, $circle->getCircleRadius() );
	}

	public function testGivenInvalid_radiusIsOne() {
		$circle = $this->newCircleParser()->parse( '42,42:foo' );

		$this->assertSame( 1.0, $circle->getCircleRadius() );
	}

}
