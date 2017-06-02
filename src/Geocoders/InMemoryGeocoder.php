<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;

/**
 * @since 3.8
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InMemoryGeocoder implements Geocoder {

	private $locations;

	/**
	 * @param LatLongValue[] $locations
	 */
	public function __construct( array $locations ) {
		$this->locations = $locations;
	}

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		if ( array_key_exists( $address, $this->locations ) ) {
			return $this->locations[$address];
		}

		return null;
	}

}
