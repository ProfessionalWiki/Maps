<?php

namespace Maps\Presentation\WikitextParsers;

use Maps\Elements\WmsOverlay;
use ValueParsers\ParseException;
use ValueParsers\ValueParser;

/**
 * ValueParser that parses the string representation of a WMS layer
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WmsOverlayParser implements ValueParser {

	/**
	 * Parses the provided string and returns the result.
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return WmsOverlay
	 * @throws ParseException
	 */
	public function parse( $value ) {
		if ( !is_string( $value ) ) {
			throw new ParseException( 'Not a string' );
		}

		$separator = " ";
		$metaData = explode( $separator, $value );

		if ( count( $metaData ) >= 2 ) {
			$wmsOverlay = new WmsOverlay( $metaData[0], $metaData[1] );
			if ( count( $metaData ) == 3 ) {
				$wmsOverlay->setWmsStyleName( $metaData[2] );
			}

			return $wmsOverlay;
		}

		throw new ParseException( 'Need at least two parameters, url to WMS server and map layer name' );
	}

}
