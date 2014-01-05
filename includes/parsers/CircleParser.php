<?php

namespace Maps;

use Maps\Elements\Circle;
use ValueParsers\StringValueParser;

/**
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Kim Eik
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParser extends StringValueParser {

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
	 * @return Circle
	 */
	public function stringParse( $value ) {
//		$parts = explode( $this->metaDataSeparator , $value );
//
//		$line = $this->constructShapeFromLatLongValues( $this->parseCoordinates(
//			explode( ':' , array_shift( $parts ) )
//		) );
//
//		$this->handleCommonParams( $parts, $line );
//
//		return $line;
	}

}
