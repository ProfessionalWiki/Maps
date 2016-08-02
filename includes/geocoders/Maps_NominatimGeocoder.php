<?php

/**
 * Class for geocoding requests with the OSM Nominatim.
 * 
 * Webservice documentation: http://wiki.openstreetmap.org/wiki/Nominatim
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */
final class MapsNominatimGeocoder extends \Maps\Geocoder {
	
	/**
	 * Registers the geocoder.
	 * 
	 * No LSB in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 * 
	 * @since 0.7
	 */
	public static function register() {
		\Maps\Geocoders::registerGeocoder( 'nominatim', __CLASS__ );
		return true;
	}
	
	/**
	 * @see \Maps\Geocoder::getRequestUrl
	 * 
	 * @since 0.7
	 * 
	 * @param string $address
	 * 
	 * @return string
	 */	
	protected function getRequestUrl( $address ) {
		$urlArgs = [
			'q' => urlencode( $address ),
			'format' => 'jsonv2',
			'limit' => 1,
		];

		return 'https://nominatim.openstreetmap.org/search?' . wfArrayToCgi($urlArgs);
	}
	
	/**
	 * @see \Maps\Geocoder::parseResponse
	 * 
	 * @since 0.7
	 * 
	 * @param string $response
	 * 
	 * @return array
	 */
	protected function parseResponse( $response ) {
		$jsonResponse = json_decode( $response );

		if (count($jsonResponse) < 1) {
			return false;
		}

		$location  = $jsonResponse[0];

		$lon = $location->lon;
		$lat = $location->lat;

		// In case on of the values is not found, return false.
		if ( !$lon || !$lat ) return false;

		return [
			'lat' => (float)$lat,
			'lon' => (float)$lon
		];
	}
	
}
