<?php

namespace Maps;

use MWException;
use ValueParsers\GeoCoordinateParser;
use ValueParsers\ParseException;
use ValueParsers\StringValueParser;

/**
 * ValueParser that parses the string representation of a WMS layer
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
 * @file
 * @ingroup ValueParsers
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WmsOverlayParser extends StringValueParser {

	/**
	 * Parses the provided string and returns the result.
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return WmsOverlay
	 */
	protected function stringParse( $value ) {
		$separator = " ";
		$metaData = explode($separator, $value);

		if ( count( $metaData ) >= 2 ) {
			$wmsOverlay = new WmsOverlay( $metaData[0], $metaData[1] );
			if ( count( $metaData ) == 3) {
				$wmsOverlay->setWmsStyleName( $metaData[2] );
			}

			return $wmsOverlay;
		}

		throw new ParseException( 'Need at least two parameters, url to WMS server and map layer name' );
	}
}
