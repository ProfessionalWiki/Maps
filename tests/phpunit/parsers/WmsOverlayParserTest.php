<?php

namespace Maps\Test;

use ValueParsers\Result;

/**
 * Unit tests for the Maps\WmsOverlayParser class.
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
 * @group WmsOverlayParserTest
 *
 * @licence GNU GPL v2+
 * @author Mathias Mølster Lidal <mathiaslidal@gmail.com>
 */
class WmsOverlayParserTest extends \ValueParsers\Test\StringValueParserTest {

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
			"http://demo.cubewerx.com/demo/cubeserv/cubeserv.cgi? Foundation.GTOPO30" =>
				array( "http://demo.cubewerx.com/demo/cubeserv/cubeserv.cgi?", "Foundation.GTOPO30" ),
			"http://maps.imr.no:80/geoserver/wms? vulnerable_areas:Identified_coral_area coral_identified_areas" =>
				array( "http://maps.imr.no:80/geoserver/wms?", "vulnerable_areas:Identified_coral_area", "coral_identified_areas" )
		);

		foreach ( $valid as $value => $expected ) {
			$expectedOverlay = new \Maps\WmsOverlay( $expected[0], $expected[1] );
			if ( count( $expected ) == 3 ) {
				$expectedOverlay->setWmsStyleName( $expected[2] );
			}
			$argLists[] = array( (string)$value, Result::newSuccess( $expectedOverlay ) );
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
		return 'Maps\WmsOverlayParser';
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
