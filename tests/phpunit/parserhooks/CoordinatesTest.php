<?php

namespace Maps\Test;

/**
 * Tests for the Maps\Coordinates class.
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
 * @group CoordinatesTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CoordinatesTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::getInstance
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected function getInstance() {
		return new \MapsCoordinates();
	}

	/**
	 * @see ParserHookTest::parametersProvider
	 * @since 2.0
	 * @return array
	 */
	public function parametersProvider() {
		$paramLists = array();

		$paramLists[] = array( 'location' => '4,2' );
		$paramLists[] = array( 'location' => '55 S, 37.6176330 W' );

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 * @since 3.0
	 * @return array
	 */
	public function processingProvider() {
		$definitions = \ParamDefinition::getCleanDefinitions( $this->getInstance()->getParamDefinitions() );
		$argLists = array();

		$values = array(
			'location' => '4,2',
		);

		$expected = array(
			'location' => new \DataValues\GeoCoordinateValue( 4, 2 ),
		);

		$argLists[] = array( $values, $expected );

		$values = array(
			'location' => '4,2',
			'directional' => $definitions['directional']->getDefault() ? 'no' : 'yes',
			'format' => 'dd',
		);

		$expected = array(
			'location' => new \DataValues\GeoCoordinateValue( 4, 2 ),
			'directional' => !$definitions['directional']->getDefault(),
			'format' => Maps_COORDS_DD,
		);

		$argLists[] = array( $values, $expected );

		$values = array(
			'location' => '4,2',
			'directional' => $definitions['directional']->getDefault() ? 'NO' : 'YES',
			'format' => ' DD ',
		);

		$expected = array(
			'location' => new \DataValues\GeoCoordinateValue( 4, 2 ),
			'directional' => !$definitions['directional']->getDefault(),
			'format' => Maps_COORDS_DD,
		);

		$argLists[] = array( $values, $expected );

		return $argLists;
	}

}