<?php

namespace Maps\Tests\Unit\Elements;

use DataValues\Geo\Values\LatLongValue;
use Maps\Elements\Location;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Elements\Location
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocationTest extends TestCase {

	public function latLongValueProvider() {
		$argLists = [];

		$argLists[] = [ new LatLongValue( 0, 0 ) ];
		$argLists[] = [ new LatLongValue( 4, 2 ) ];
		$argLists[] = [ new LatLongValue( 42, 42 ) ];
		$argLists[] = [ new LatLongValue( -4.2, -42 ) ];

		return $argLists;
	}

	/**
	 * @dataProvider latLongValueProvider
	 */
	public function testGivenLatLongInConstructor_getCoordinatesReturnsIt( LatLongValue $latLong ) {
		$location = new Location( $latLong );
		$this->assertTrue( $latLong->equals( $location->getCoordinates() ) );
	}

}
