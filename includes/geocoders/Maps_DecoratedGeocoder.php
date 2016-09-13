<?php

use Maps\Geocoders\Geocoder;

/**
 * @since 3.8
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsDecoratedGeocoder extends \Maps\Geocoder {

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
