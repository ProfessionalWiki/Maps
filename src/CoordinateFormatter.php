<?php

declare( strict_types = 1 );

namespace Maps;

use DataValues\Geo\Formatters\LatLongFormatter;
use DataValues\Geo\Values\LatLongValue;
use ValueFormatters\FormatterOptions;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CoordinateFormatter {

	public function format( LatLongValue $latLong, string $format, bool $directional ) {
		$formatter = new LatLongFormatter( new FormatterOptions(
			[
				LatLongFormatter::OPT_FORMAT => $format,
				LatLongFormatter::OPT_DIRECTIONAL => $directional,
				LatLongFormatter::OPT_PRECISION => 1 / 360000
			]
		) );

		return $formatter->format( $latLong );
	}

}
