<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StubGeocoder implements Geocoder {

	private $returnValue;

	public function __construct( LatLongValue $returnValue ) {
		$this->returnValue = $returnValue;
	}

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		return $this->returnValue;
	}

}
