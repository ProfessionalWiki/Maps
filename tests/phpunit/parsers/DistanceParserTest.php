<?php

namespace Maps\Test;

use ValueParsers\Result;

/**
 * Unit tests for the Maps\DistanceParser class.
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
 * @group ValueParsers
 * @group Maps
 * @group DistanceParserTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DistanceParserTest extends \ValueParsers\Test\StringValueParserTest {

	/**
	 * @see ValueParserTestBase::parseProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function parseProvider() {
		$argLists = array();

		$valid = array(
			'1' => 1,
			'1m' => 1,
			'42 km' => 42000,
			'4.2 km' => 4200,
			'4.2 m' => 4.2,
		);

		foreach ( $valid as $value => $expected ) {
			$argLists[] = array( (string)$value, Result::newSuccess( $expected ) );
		}

		return array_merge( $argLists, parent::parseProvider() );
	}

	/**
	 * @see ValueParserTestBase::getParserClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	protected function getParserClass() {
		return 'Maps\DistanceParser';
	}

	/**
	 * @see ValueParserTestBase::requireDataValue
	 *
	 * @since 3.0
	 *
	 * @return boolean
	 */
	protected function requireDataValue() {
		return false;
	}

}
