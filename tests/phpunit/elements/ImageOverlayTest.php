<?php

namespace Maps\Test;

use Maps\ImageOverlay;

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
class ImageOverlayTest extends RectangleTest {

	/**
	 * @see BaseElementTest::getClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getClass() {
		return 'Maps\ImageOverlay';
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

		foreach ( parent::constructorProvider() as $argList ) {
			$argList[0] = false;
			$argLists[] = $argList;
		}

		foreach ( parent::constructorProvider() as $argList ) {
			$argList[] = 'Foo.png';
			$argLists[] = $argList;
		}

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 * @param \Maps\ImageOverlay $imageOverlay
	 * @param array $arguments
	 */
	public function testGetImage( ImageOverlay $imageOverlay, array $arguments ) {
		$this->assertEquals( $arguments[2], $imageOverlay->getImage() );
	}

}



