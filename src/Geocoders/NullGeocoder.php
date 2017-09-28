<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NullGeocoder implements Geocoder {

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		return null;
	}

}
