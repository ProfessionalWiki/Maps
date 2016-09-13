<?php

use Maps\Geocoders\Geocoder;

/**
 * Adapts the new Maps\Geocoders\Geocoder interface to the legacy
 * Maps\Geocoder class hierarchy.
 *
 * @since 3.8
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsOldGeocoderAdapter extends \Maps\Geocoder {

	private $geocoder;

	/**
	 * @param Geocoder $geocoder
	 * @param string $identifier
	 */
	public function __construct( Geocoder $geocoder, $identifier ) {
		$this->geocoder = $geocoder;

		parent::__construct( $identifier );
	}

	public function geocode( $address ) {
		$result = $this->geocoder->geocode( $address );

		if ( $result === null ) {
			return false;
		}

		return [
			'lat' => $result->getLatitude(),
			'lon' => $result->getLongitude(),
		];
	}

	protected function getRequestUrl( $address ) {}

	protected function parseResponse( $response ) {}

}
