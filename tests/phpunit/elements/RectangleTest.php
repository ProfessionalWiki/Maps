<?php

namespace Maps\Test;

use DataValues\LatLongValue;
use Maps\Rectangle;

/**
 * Unit tests for the Maps\Rectangle class.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
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
		return 'Maps\Rectangle';
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
	 * @param \Maps\Rectangle $rectangle
	 * @param array $arguments
	 */
	public function testGetCorners( Rectangle $rectangle, array $arguments ) {
		$this->assertTrue( $rectangle->getRectangleNorthEast()->equals( $arguments[0] ) );
		$this->assertTrue( $rectangle->getRectangleSouthWest()->equals( $arguments[1] ) );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param \Maps\Rectangle $rectangle
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



