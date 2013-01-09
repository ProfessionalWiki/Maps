<?php

namespace Maps\Test;
use ValueParsers\Result;
use Maps\Line;

/**
 * Unit tests for the Maps\LineParser class.
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
 * @group LineParserTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LineParserTest extends \ValueParsers\Test\StringValueParserTest {

	/**
	 * @see ValueParserTestBase::parseProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function parseProvider() {
		$argLists = array();

		$valid = array();

		$valid[] = array(
			array(
				42,
				4.2
			),
		);

		$valid[] = array(
			array(
				49.83798245308486,
				2.724609375
			),
			array(
				52.05249047600102,
				8.26171875
			),
			array(
				46.37725420510031,
				6.15234375
			),
			array(
				49.83798245308486,
				2.724609375
			),
		);

		foreach ( $valid as $values ) {
			$input = array();
			$output = array();

			foreach ( $values as $value ) {
				$input[] = implode( ',', $value );
				$output[] = new \DataValues\GeoCoordinateValue( $value[0], $value[1] );
			}

			$input = implode( ':', $input );

			$argLists[] = array( $input, Result::newSuccess( new Line( $output ) ) );
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
		return 'Maps\LineParser';
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
