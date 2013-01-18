<?php

namespace Maps\Test;

/**
 * Tests for the Maps\DisplayMap class.
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
 * @file
 * @since 2.0
 *
 * @ingroup Maps
 * @ingroup Test
 *
 * @group Maps
 * @group ParserHook
 * @group DisplayMapTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DisplayMapTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::getInstance
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected function getInstance() {
		return new \MapsDisplayMap();
	}

	/**
	 * @see ParserHookTest::parametersProvider
	 * @since 2.0
	 * @return array
	 */
	public function parametersProvider() {
		$paramLists = array();

		// TODO
		$paramLists[] = array( 'coordinates' => '4,2' );

		$paramLists[] = array( 'location' => '4,2' );

		$paramLists[] = array( 'location' => 'new york city' );

		$paramLists[] = array(
			'service' => 'googlemaps',
			'location' => 'new york city',
			'zoom' => '10',
			'minzoom' => '5',
			'maxzoom' => '7',
			'autozoom' => 'off',
		);

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 * @since 3.0
	 * @return array
	 */
	public function processingProvider() {
		$definitions = $this->getInstance()->getParamDefinitions();
		$argLists = array();

		// TODO
		$values = array(
			'locations' => '4,2',
			'width' => '420',
			'height' => '420',
		);

		$expected = array(
			'coordinates' => array( new \Maps\Location( new \DataValues\GeoCoordinateValue( 4, 2 ) ) ),
			'width' => '420px',
			'height' => '420px',
		);

		$argLists[] = array( $values, $expected );

		return $argLists;
	}

}