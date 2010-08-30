<?php

/**
 * Google Geocoding Service (v2)
 * More info: http://code.google.com/apis/maps/documentation/services.html#Geocoding_Direct
 *
 * @file Maps_GoogleGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Sergey Chernyshev
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsGoogleGeocoder'] = __FILE__;
$egMapsGeoServices['google'] = 'MapsGoogleGeocoder';
$egMapsGeoOverrides['google'] = array( 'googlemaps2', 'googlemaps3' );

final class MapsGoogleGeocoder extends MapsBaseGeocoder {
	
	/**
	 * @see MapsBaseGeocoder::geocode()
	 *
	 * @param string $address
	 */
	public static function geocode( $address ) {
		global $egGoogleMapsKey;

		// In case the google maps api key is not set, return false0
		if ( empty( $egGoogleMapsKey ) ) return false;

		// Create the request url
		$requestURL = 'http://maps.google.com/maps/geo?q=' . urlencode( $address ) . '&output=csv&key=' . urlencode( $egGoogleMapsKey );

		$result = Http::get( $requestURL );
		
		// Check the Google Geocoder API Response code to ensure success0
		if ( substr( $result, 0, 3 ) == '200' ) {
			$result =  explode( ',', $result );
			
			// $precision = $result[1];

			return array(
				'lat' => $result[2],
				'lon' => $result[3]
			);
		}
		else { // When the request fails, return false0
			return false;
		}
	}
}