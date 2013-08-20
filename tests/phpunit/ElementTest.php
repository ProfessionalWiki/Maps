<?php

/**
 * Unit tests for the Maps\Element implementing classes.
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
use DataValues\LatLongValue;
use Maps\Element;

class ElementTest extends \MediaWikiTestCase {

	public function elementProvider() {
		$elements = array();

		$elements[] = new \Maps\Rectangle( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ) );
		$elements[] = new \Maps\ImageOverlay( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ), 'foo' );
		$elements[] = new \Maps\Circle( new LatLongValue( 4, 2 ), 42 );
		$elements[] = new \Maps\Line( array( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ) ) );
		//$elements[] = new \Maps\Polygon( array( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ) ) );
		// TODO: location

		return $this->arrayWrap( $elements );
	}

	/**
	 * @dataProvider elementProvider
	 * @param Element $element
	 */
	public function getArrayValue( Element $element ) {
		$this->assertEquals( $element->getArrayValue(), $element->getArrayValue() );
	}

	/**
	 * @dataProvider elementProvider
	 * @param Element $element
	 */
	public function testSetOptions( Element $element ) {
		$options = new \Maps\ElementOptions();
		$options->setOption( 'foo', 'bar' );
		$options->setOption( '~=[,,_,,]:3', 42 );

		$element->setOptions( $options );

		$this->assertEquals( $element->getOptions()->getOption( 'foo' ), 'bar' );
		$this->assertEquals( $element->getOptions()->getOption( '~=[,,_,,]:3' ), 42 );

		$options = clone $options;
		$options->setOption( 'foo', 'baz' );

		$element->setOptions( $options );

		$this->assertEquals( $element->getOptions()->getOption( 'foo' ), 'baz' );
	}

	/**
	 * @dataProvider elementProvider
	 * @param Element $element
	 */
	public function testGetOptions( Element $element ) {
		$this->assertInstanceOf( '\Maps\ElementOptions', $element->getOptions() );
	}

}
