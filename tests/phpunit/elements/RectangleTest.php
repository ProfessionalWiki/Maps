<?php

namespace Maps\Tests\Elements;

use DataValues\LatLongValue;
use Maps\Elements\Rectangle;

/**
 * @covers Maps\Elements\Rectangle
 *
 * @since 3.0
 *
 * @ingroup MapsTest
 *
 * @group Maps
 * @group MapsElement
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
		return 'Maps\Elements\Rectangle';
	}

	/**
	 * @see BaseElementTest::constructorProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array( false );
		$argLists[] = array( false, '' );
		$argLists[] = array( false, '4,2' );
		$argLists[] = array( false, new LatLongValue( 4, 2 ) );
		$argLists[] = array( false, array() );
		$argLists[] = array( false, array( new LatLongValue( 4, 2 ) ) );
		$argLists[] = array( false, new LatLongValue( 4, 2 ), 'foobar' );
		$argLists[] = array( false, 'foobar', new LatLongValue( 4, 2 ) );

		$argLists[] = array( true, new LatLongValue( 4, 2 ), new LatLongValue( 4, 2 ) );
		$argLists[] = array( true, new LatLongValue( 4, 2 ), new LatLongValue( -4, -2 ) );

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 * @param Rectangle $rectangle
	 * @param array $arguments
	 */
	public function testGetCorners( Rectangle $rectangle, array $arguments ) {
		$this->assertTrue( $rectangle->getRectangleNorthEast()->equals( $arguments[0] ) );
		$this->assertTrue( $rectangle->getRectangleSouthWest()->equals( $arguments[1] ) );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param Rectangle $rectangle
	 * @param array $arguments
	 */
	public function testSetCorners( Rectangle $rectangle, array $arguments ) {
		$coordinates = array(
			new LatLongValue( 42, 42 ),
			new LatLongValue( 0, 0 )
		);

		foreach ( $coordinates as $coordinate ) {
			$rectangle->setRectangleNorthEast( $coordinate );
			$this->assertTrue( $rectangle->getRectangleNorthEast()->equals( $coordinate ) );

			$rectangle->setRectangleSouthWest( $coordinate );
			$this->assertTrue( $rectangle->getRectangleSouthWest()->equals( $coordinate ) );
		}
	}

}



