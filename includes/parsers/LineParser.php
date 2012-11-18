<?php

namespace Maps;
use ValueParsers\StringValueParser;
use ValueParsers\Result;
use ValueParsers\ResultObject;

use iFillableMapElement, iHoverableMapElement;

/**
 * ValueParser that parses the string representation of a line.
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
 * @ingroup Maps
 * @ingroup ValueParser
 *
 * @licence GNU GPL v2+
 * @author Kim Eik
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LineParser extends StringValueParser {

	// TODO: use options
	protected $metaDataSeparator = '~';

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Result
	 */
	public function stringParse( $value ) {
		if ( $this->canBeParsed( $value ) ) {
			$parts = explode( $this->metaDataSeparator , $value );

			$coordinateStrings = explode( ':' , array_shift( $parts ) );

			$coordinates = array();
			$coordinateParser = new \ValueParsers\GeoCoordinateParser();

			foreach ( $coordinateStrings as $coordinateString ) {
				$parseResult = $coordinateParser->parse( $coordinateString );

				if ( $parseResult->isValid() ) {
					$coordinates[] = $parseResult->getValue();
				}
				else {
					// TODO
				}
			}

			$line = new Line( $coordinates );

			$this->handleCommonParams( $parts, $line );

			return ResultObject::newSuccess( $line->getJSONObject() );
		}
		else {
			return ResultObject::newErrorText( 'Not a line' ); // TODO
		}
	}

	// TODO: integrate with parse, have more specific error and possibly just drop bad points rather then fail completely
	protected function canBeParsed( $value )  {
		// Split off meta-data
		$value = explode( $this->metaDataSeparator, $value );
		$value = $value[0];

		$parts = explode( ':', $value );

		// Need at least two points to create a line.
		if ( count( $parts ) < 2 ) {
			return false;
		}

		//setup geocode deps
		$canGeoCode = \MapsGeocoders::canGeocode();

		foreach ( $parts as $part ) {
			$toIndex = strpos( $part, $this->metaDataSeparator );

			if ( $toIndex !== false ) {
				$part = substr( $part, 0, $toIndex );
			}

			if ( $canGeoCode ){
				$valid = \MapsGeocoders::isLocation(
					$part,
					'', // TODO
					false // TODO
				);
			} else {
				$valid = \ValueParsers\GeoCoordinateParser::areCoordinates( $part );
			}

			if( !$valid ){
				return false;
			}
		}

		return true;
	}

	/**
	 * This method requires that parameters are positionally correct,
	 * 1. Link (one parameter) or bubble data (two parameters)
	 * 2. Stroke data (three parameters)
	 * 3. Fill data (two parameters)
	 * e.g ...title~text~strokeColor~strokeOpacity~strokeWeight~fillColor~fillOpacity
	 *
	 * @since 3.0
	 *
	 * @param array $params
	 * @param Line $line
	 */
	protected function handleCommonParams( array &$params, Line &$line ) {
		//Handle bubble and link parameters

		//create link data
		$linkOrTitle = array_shift( $params );
		if ( $link = $this->isLinkParameter( $linkOrTitle ) ) {
			$this->setLinkFromParameter( $line , $link );
		} else {
			//create bubble data
			$this->setBubbleDataFromParameter( $line , $params , $linkOrTitle );
		}


		//handle stroke parameters
		if ( $color = array_shift( $params ) ) {
			$line->setStrokeColor( $color );
		}

		if ( $opacity = array_shift( $params ) ) {
			$line->setStrokeOpacity( $opacity );
		}

		if ( $weight = array_shift( $params ) ) {
			$line->setStrokeWeight( $weight );
		}
	}

	protected function setBubbleDataFromParameter( Line &$line , &$params , $title ) {
		if ( $title ) {
			$line->setTitle( $title );
		}
		if ( $text = array_shift( $params ) ) {
			$line->setText( $text );
		}
	}

	protected function setLinkFromParameter( Line &$line , $link ) {
		if ( filter_var( $link , FILTER_VALIDATE_URL , FILTER_FLAG_SCHEME_REQUIRED ) ) {
			$line->setLink( $link );
		} else {
			$title = \Title::newFromText( $link );
			$line->setLink( $title->getFullURL() );
		}
	}

	/**
	 * Checks if a string is prefixed with link:
	 * @static
	 * @param $link
	 * @return bool|string
	 * @since 2.0
	 */
	private function isLinkParameter( $link ) {
		if ( strpos( $link , 'link:' ) === 0 ) {
			return substr( $link , 5 );
		}

		return false;
	}

}
