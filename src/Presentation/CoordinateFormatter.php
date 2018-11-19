<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use DataValues\Geo\Formatters\LatLongFormatter;
use DataValues\Geo\Values\LatLongValue;
use ValueFormatters\FormatterOptions;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CoordinateFormatter {

	private const PRECISION_MAP = [
		'dms' => 1 / 360000,
		'dm' => 1 / 600000,
		'dd' => 1 / 1000000,
		'float' => 1 / 1000000,
	];

	public function format( LatLongValue $latLong, string $format, bool $directional ) {
		$formatter = new LatLongFormatter( new FormatterOptions(
			[
				LatLongFormatter::OPT_FORMAT => $format,
				LatLongFormatter::OPT_DIRECTIONAL => $directional,
				LatLongFormatter::OPT_PRECISION => self::PRECISION_MAP[$format]
			]
		) );

		return $formatter->format( $latLong );
	}

}
