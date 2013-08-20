<?php

namespace Maps\Test;

use DataValues\LatLongValue;
use Maps\Line;

/**
 * Unit tests for the Maps\Line class.
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
class LineTest extends BaseElementTest {

	/**
	 * @see BaseElementTest::getClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getClass() {
		return 'Maps\Line';
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

		$argLists[] = array( true, array() );
		$argLists[] = array( true, array( new LatLongValue( 4, 2 ) ) );

		$argLists[] = array(
			true,
			array(
				new LatLongValue( 4, 2 ),
				new LatLongValue( 2, 4 ),
				new LatLongValue( 42, 42 ),
			)
		);

		$argLists[] = array( false, array( '~=[,,_,,]:3' ) );
		$argLists[] = array( false, array( new LatLongValue( 4, 2 ), '~=[,,_,,]:3' ) );
		$argLists[] = array( false, array( '~=[,,_,,]:3', new LatLongValue( 4, 2 ) ) );

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 * @param \Maps\Line $line
	 * @param array $arguments
	 */
	public function testGetLineCoordinates( Line $line, array $arguments ) {
		$coordinates = $line->getLineCoordinates();

		$this->assertInternalType( 'array', $coordinates );
		$this->assertEquals( count( $arguments[0] ), count( $coordinates ) );

		foreach ( $coordinates as $geoCoordinate ) {
			$this->assertInstanceOf( 'DataValues\LatLongValue', $geoCoordinate );
		}
	}

}



