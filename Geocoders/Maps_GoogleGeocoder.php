<?php

/**
 * Google Geocoding Service info: http://code.google.com/apis/maps/documentation/services.html#Geocoding_Direct
 *
 * @file Maps_GoogleGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Sergey Chernyshev
 * 
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsGoogleGeocoder {

	function callGeocoder($address) {
		global $GeocoderCache, $egGoogleMapsKey;

		// In case the google maps api key is not set, return false
		if (empty($egGoogleMapsKey)) return false;

		// Create the request url
		$requestURL = 'http://maps.google.com/maps/geo?q='.urlencode($address).'&output=csv&key='.urlencode($egGoogleMapsKey);

		if ($handle = fopen($requestURL, "r")) {
			$result = fread($handle, 10000);
			fclose($handle);

			$values = split(',', $result);

			// If the status is not 200, return false
			if ($values[0] !== '200') return false;

			return $values;
		}
		else { // When the request fails, return false
			return false;
		}
	}

}