<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;
use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;

/**
 * Webservice documentation: http://code.google.com/apis/maps/documentation/geocoding/
 *
 * @since 4.5
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GoogleGeocoder implements Geocoder {

	private $fileFetcher;
	private $apiKey;
	private $apiVersion;

	/**
	 * @param FileFetcher $fileFetcher
	 * @param string $apiKey
	 * @param string $apiVersion
	 */
	public function __construct( FileFetcher $fileFetcher, $apiKey, $apiVersion ) {
		$this->fileFetcher = $fileFetcher;
		$this->apiKey = $apiKey;
		$this->apiVersion = $apiVersion;
	}

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		try {
			$response = $this->fileFetcher->fetchFile( $this->getRequestUrl( $address ) );
		}
		catch ( FileFetchingException $ex ) {
			return null;
		}

		$jsonResponse = json_decode( $response, true );

		if ( !is_array( $jsonResponse ) ) {
			return null;
		}

		if ( !array_key_exists( 'results', $jsonResponse ) || count( $jsonResponse['results'] ) < 1 ) {
			return null;
		}

		$location = @$jsonResponse['results'][0]['geometry']['location'];

		if ( !is_array( $location )
			 || !array_key_exists( 'lat', $location ) || !array_key_exists( 'lng', $location ) ) {
			return null;
		}

		return new LatLongValue( (float)$location['lat'], (float)$location['lng'] );
	}

	/**
	 * @param string $address
	 *
	 * @return string
	 */
	private function getRequestUrl( $address ) {
		$urlArgs = [
			'address' => $address,
			'key' => $this->apiKey,
		];

		if ( $this->apiVersion !== '' ) {
			$urlArgs['v'] = $this->apiVersion;
		}

		return 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query( $urlArgs );
	}

}
