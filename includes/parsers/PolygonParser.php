<?php

namespace Maps;

use Maps\Elements\Polygon;
use ValueParsers\StringValueParser;

/**
 * ValueParser that parses the string representation of a polygon.
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PolygonParser extends LineParser {

	protected function constructShapeFromLatLongValues( array $locations ) {
		return new Polygon( $locations );
	}

	// TODO: handle only visible on hover

}
