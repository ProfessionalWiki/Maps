<?php

namespace Maps\Test;

/**
 * Tests for the Maps\Distance class.
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
 * @group MapsDistanceTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DistanceTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::getInstance
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected function getInstance() {
		return new \MapsDistance();
	}

	/**
	 * @since 3.0
	 * @var array
	 */
	protected $distances = array(
		'42' => 42,
		'42m' => 42,
		'42 m' => 42,
		'42 km' => 42000,
		'4.2 km' => 4200,
		'4.2 m' => 4.2,
	);

	/**
	 * @see ParserHookTest::parametersProvider
	 * @since 2.0
	 * @return array
	 */
	public function parametersProvider() {
		$paramLists = array();

		foreach ( array_keys( $this->distances ) as $distance ) {
			$paramLists[] = array( 'distance' => (string)$distance );
		}

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 * @since 3.0
	 * @return array
	 */
	public function processingProvider() {
		$argLists = array();

		foreach ( $this->distances as $input => $output ) {
			$values = array(
				'distance' => (string)$input,
			);

			$expected = array(
				'distance' => $output,
			);

			$argLists[] = array( $values, $expected );
		}

		$values = array(
			'distance' => '42m',
			'unit' => 'km',
			'decimals' => '1',
		);

		$expected = array(
			'distance' => 42,
			'unit' => 'km',
			'decimals' => 1,
		);

		$argLists[] = array( $values, $expected );

		$values = array(
			'distance' => '42m',
			'unit' => '~=[,,_,,]:3',
			'decimals' => 'foobar',
		);

		$expected = array(
			'distance' => 42,
		);

		$argLists[] = array( $values, $expected );

		return $argLists;
	}

}