<?php

namespace Maps\Test;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoders\Decorators\CoordinateFriendlyGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\NullGeocoder;
use Maps\CircleParser;
use Maps\Elements\Circle;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\CircleParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParserTest extends TestCase {

	public function testGivenCoordinateAndRadius_parserReturnsCircle() {
		$parser = new CircleParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );

		$circle = $parser->parse( '57.421,23.90625:32684.605182' );

		$this->assertInstanceOf( Circle::class, $circle );

		$expectedLatLong = new LatLongValue( 57.421, 23.90625 );
		$this->assertTrue( $expectedLatLong->equals( $circle->getCircleCentre() ) );

		$this->assertSame( 32684.605182, $circle->getCircleRadius() );
	}

	public function testGivenTitleAndText_circleHasProvidedMetaData() {
		$parser = new CircleParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );

		$circle = $parser->parse( '57.421,23.90625:32684.605182~title~text' );

		$this->assertInstanceOf( Circle::class, $circle );

		$this->assertSame( 'title', $circle->getTitle() );
		$this->assertSame( 'text', $circle->getText() );
	}

}
