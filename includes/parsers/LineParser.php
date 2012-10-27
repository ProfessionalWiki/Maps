<?php

namespace Maps;
use ValueParsers\StringValueParser;
use ValueParsers\Result;
use ValueParsers\ResultObject;

use iBubbleMapElement, iLinkableMapElement, iStrokableMapElement, iFillableMapElement, iHoverableMapElement;

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
	protected $metaDataSeparator;

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
			$lineCoords = explode( ':' , array_shift( $parts ) );

			$value = new \MapsLine( $lineCoords );
			$this->handleCommonParams( $parts , $value );

			$value = $value->getJSONObject();

			return ResultObject::newSuccess( new \DataValues\UnknownValue( $value ) );
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

		$parts = explode(':', $value);

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
	 * @static
	 * @param $obj
	 * @param $metadataParams
	 */
	protected function handleCommonParams( array &$params , &$model ) {

		//Handle bubble and link parameters
		if ( $model instanceof iBubbleMapElement && $model instanceof iLinkableMapElement ) {
			//create link data
			$linkOrTitle = array_shift( $params );
			if ( $link = $this->isLinkParameter( $linkOrTitle ) ) {
				$this->setLinkFromParameter( $model , $link );
			} else {
				//create bubble data
				$this->setBubbleDataFromParameter( $model , $params , $linkOrTitle );
			}
		} else if ( $model instanceof iLinkableMapElement ) {
			//only supports links
			$link = array_shift( $params );
			if ( $link = $this->isLinkParameter( $link ) ) {
				$this->setLinkFromParameter( $model , $link );
			}
		} else if ( $model instanceof iBubbleMapElement ) {
			//only supports bubbles
			$title = array_shift( $params );
			$this->setBubbleDataFromParameter( $model , $params , $title );
		}

		//handle stroke parameters
		if ( $model instanceof iStrokableMapElement ) {
			if ( $color = array_shift( $params ) ) {
				$model->setStrokeColor( $color );
			}

			if ( $opacity = array_shift( $params ) ) {
				$model->setStrokeOpacity( $opacity );
			}

			if ( $weight = array_shift( $params ) ) {
				$model->setStrokeWeight( $weight );
			}
		}

		//handle fill parameters
		if ( $model instanceof iFillableMapElement ) {
			if ( $fillColor = array_shift( $params ) ) {
				$model->setFillColor( $fillColor );
			}

			if ( $fillOpacity = array_shift( $params ) ) {
				$model->setFillOpacity( $fillOpacity );
			}
		}

		//handle hover parameter
		if ( $model instanceof iHoverableMapElement ) {
			if ( $visibleOnHover = array_shift( $params ) ) {
				$model->setOnlyVisibleOnHover( filter_var( $visibleOnHover , FILTER_VALIDATE_BOOLEAN ) );
			}
		}
	}

}
