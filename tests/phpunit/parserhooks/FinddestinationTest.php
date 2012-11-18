<?php

namespace Maps\Test;

/**
 * Tests for the Maps\Finddestination class.
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
 * @group MapsFinddestinationTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FinddestinationTest extends ParserHookTest {

	/**
	 * @since 3.0
	 * @var string[]
	 */
	protected $locations = array(
		'4,2',
		'4.2,-42',
	);

	/**
	 * @since 3.0
	 * @var array
	 */
	protected $bearings = array(
		1,
		42,
		-42,
		0,
		4.2,
	);

	/**
	 * @since 3.0
	 * @var string[]
	 */
	protected $distances = array(
		'42' => 42,
		'0' => 0,
		'42 m' => 42,
		'42 km' => 42000,
		'4.2 km' => 4200,
	);

	/**
	 * @see ParserHookTest::getInstance
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected function getInstance() {
		return new \MapsFinddestination();
	}

	/**
	 * @see ParserHookTest::parametersProvider
	 * @since 2.0
	 * @return array
	 */
	public function parametersProvider() {
		$paramLists = array();

		$paramLists[] = array(
			'location' => '4,2',
			'bearing' => '1',
			'distance' => '42 km'
		);

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 * @since 3.0
	 * @return array
	 */
	public function processingProvider() {
		$argLists = array();

		$coordinateParser = new \ValueParsers\GeoCoordinateParser();

		foreach ( $this->distances as $distance => $expectedDistance ) {
			foreach ( $this->bearings as $bearing ) {
				foreach ( $this->locations as $location ) {
					$values = array(
						'distance' => (string)$distance,
						'bearing' => (string)$bearing,
						'location' => (string)$location,
					);

					$expected = array(
						'distance' => $expectedDistance,
						'bearing' => (float)$bearing,
						'location' => new \MapsLocation( $coordinateParser->parse( $location )->getValue() ),
					);

					$argLists[] = array( $values, $expected );
				}
			}
		}

		return $argLists;
	}

}