<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\LegacyModel;

use DataValues\Geo\Values\LatLongValue;
use Maps\LegacyModel\Rectangle;

/**
 * @covers \Maps\LegacyModel\Rectangle
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RectangleTest extends BaseElementTest {

	/**
	 * @see BaseElementTest::getClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getClass() {
		return Rectangle::class;
	}

	public function validConstructorProvider() {
		$argLists = [];

		$argLists[] = [ new LatLongValue( 4, 2 ), new LatLongValue( -4, -2 ) ];
		$argLists[] = [ new LatLongValue( -42, -42 ), new LatLongValue( -4, -2 ) ];

		return $argLists;
	}

	public function invalidConstructorProvider() {
		$argLists = [];

		$argLists[] = [ new LatLongValue( 4, 2 ), new LatLongValue( 4, 2 ) ];

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetCorners( Rectangle $rectangle, array $arguments ) {
		$this->assertTrue( $rectangle->getRectangleNorthEast()->equals( $arguments[0] ) );
		$this->assertTrue( $rectangle->getRectangleSouthWest()->equals( $arguments[1] ) );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSetCorners( Rectangle $rectangle ) {
		$coordinates = [
			new LatLongValue( 42, 42 ),
			new LatLongValue( 0, 0 )
		];

		foreach ( $coordinates as $coordinate ) {
			$rectangle->setRectangleNorthEast( $coordinate );
			$this->assertTrue( $rectangle->getRectangleNorthEast()->equals( $coordinate ) );

			$rectangle->setRectangleSouthWest( $coordinate );
			$this->assertTrue( $rectangle->getRectangleSouthWest()->equals( $coordinate ) );
		}
	}

}
