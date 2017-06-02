<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;

/**
 * @since 3.8
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Geocoder {

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 *
	 * TODO: specify failure behaviour. Exception or null?
	 */
	public function geocode( $address );

}