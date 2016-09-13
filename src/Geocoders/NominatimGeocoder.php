<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;
use FileFetcher\FileFetcher;

/**
 * Webservice documentation: http://wiki.openstreetmap.org/wiki/Nominatim
 *
 * @since 3.8
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NominatimGeocoder implements Geocoder {

	private $fileFetcher;

	public function __construct( FileFetcher $fileFetcher ) {
		$this->fileFetcher = $fileFetcher;
	}

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		$response = $this->fileFetcher->fetchFile( $this->getRequestUrl( $address ) );

		$jsonResponse = json_decode( $response );

		if ( !is_array( $jsonResponse ) || count( $jsonResponse ) < 1 ) {
			return null;
		}

		$location  = $jsonResponse[0];

		if ( !$location->lat || !$location->lon ) return null;

		return new LatLongValue( (float)$location->lat, (float)$location->lon );
	}

	/**
	 * @param string $address
	 *
	 * @return string
	 */
	private function getRequestUrl( $address ) {
		return 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' . urlencode( $address );
	}

}
