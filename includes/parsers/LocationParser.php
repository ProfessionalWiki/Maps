<?php

namespace Maps;
use ValueParsers\StringValueParser;
use ValueParsers\Result;
use ValueParsers\ResultObject;
use ValueParsers\GeoCoordinateParser;
use ValueParsers\Error;
use DataValues\GeoCoordinateValue;
use MWException;

/**
 * ValueParser that parses the string representation of a location.
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
class LocationParser extends StringValueParser {

	// TODO
	protected $supportGeocoding = true;

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Result
	 * @throws MWException
	 */
	public function stringParse( $value ) {
		$separator = '~';

		$metaData = explode( $separator, $value );

		$coordinates = $this->getCoordinates( array_shift( $metaData ) );

		if ( $coordinates instanceof Error ) {
			return ResultObject::newError( $coordinates );
		}

		$location = new \Maps\Location( $coordinates );

		if ( $metaData !== array() ) {
			$location->setTitle( array_shift( $metaData ) );
		}

		if ( $metaData !== array() ) {
			$location->setText( array_shift( $metaData ) );
		}

		if ( $metaData !== array() ) {
			$location->setIcon( array_shift( $metaData ) );
		}

		return ResultObject::newSuccess( $location );
	}

	/**
	 * @since 3.0
	 *
	 * @param string $location
	 *
	 * @return GeoCoordinateValue|Error
	 */
	protected function getCoordinates( $location ) {
		if ( $this->supportGeocoding && \MapsGeocoders::canGeocode() ) {
			$location = \MapsGeocoders::attemptToGeocode( $location );

			if ( $location === false ) {
				return $this->newErrorResult( 'Geocoding failed' )->getError();
			}

			assert( $location instanceof GeoCoordinateValue );
			return $location;
		}

		$parser = new GeoCoordinateParser();
		$parseResult = $parser->parse( $location );

		if ( !$parseResult->isValid() ) {
			return $parseResult->getError();
		}

		$location = $parseResult->getValue();
		assert( $location instanceof GeoCoordinateValue );
		return $location;
	}

}
