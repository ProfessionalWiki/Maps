<?php

namespace Maps;

use DataValues\LatLongValue;
use ValueParsers\StringValueParser;

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

	protected $supportGeocoding = true;

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Line
	 */
	public function stringParse( $value ) {
		$parts = explode( $this->metaDataSeparator , $value );

		$line = new Line( $this->parseCoordinates(
			explode( ':' , array_shift( $parts ) )
		) );

		$this->handleCommonParams( $parts, $line );

		return $line;
	}

	/**
	 * @since 3.0
	 *
	 * @param string[] $coordinateStrings
	 *
	 * @return LatLongValue[]
	 */
	protected function parseCoordinates( array $coordinateStrings ) {
		$coordinates = array();
		$coordinateParser = new \ValueParsers\GeoCoordinateParser( new \ValueParsers\ParserOptions() );

		$supportsGeocoding = $this->supportGeocoding && \Maps\Geocoders::canGeocode();

		foreach ( $coordinateStrings as $coordinateString ) {
			if ( $supportsGeocoding ) {
				$coordinate = \Maps\Geocoders::attemptToGeocode( $coordinateString );

				if ( $coordinate === false ) {
					// TODO
				}
				else {
					$coordinates[] = $coordinate;
				}
			}
			else {
				$coordinates[] = $coordinateParser->parse( $coordinateString );
			}
		}

		return $coordinates;
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
