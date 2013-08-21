<?php

namespace Maps;

use ValueParsers\StringValueParser;

/**
 * ValueParser that parses the string representation of a polygon.
 *
 * @since 3.0
 *
 * @file
 * @ingroup Maps
 * @ingroup ValueParser
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PolygonParser extends StringValueParser {

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Polygon
	 */
	public function stringParse( $value ) {
		// TODO
	}

}
