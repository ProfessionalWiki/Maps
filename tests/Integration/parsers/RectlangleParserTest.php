<?php

namespace Maps\Tests\Integration\parsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoders\Decorators\CoordinateFriendlyGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\NullGeocoder;
use Maps\Elements\Rectangle;
use Maps\Presentation\WikitextParsers\RectangleParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\WikitextParsers\RectangleParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RectangleParserTest extends TestCase {

	public function testGivenBoundingBox_parserReturnsRectangle() {
		$parser = new RectangleParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );

		$rectangle = $parser->parse( '51.8357775,33.83789:46,23.37890625' );

		$this->assertInstanceOf( Rectangle::class, $rectangle );

		$expectedNorthEast = new LatLongValue( 51.8357775, 33.83789 );
		$this->assertTrue( $expectedNorthEast->equals( $rectangle->getRectangleNorthEast() ) );

		$expectedSouthWest = new LatLongValue( 46, 23.37890625 );
		$this->assertTrue( $expectedSouthWest->equals( $rectangle->getRectangleSouthWest() ) );
	}

	public function testGivenTitleAndText_rectangleHasProvidedMetaData() {
		$parser = new RectangleParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );

		$rectangle = $parser->parse( "51.8357775,33.83789:46,23.37890625~I'm a square~of doom" );

		$this->assertInstanceOf( Rectangle::class, $rectangle );

		$this->assertSame( "I'm a square", $rectangle->getTitle() );
		$this->assertSame( 'of doom', $rectangle->getText() );
	}

}
