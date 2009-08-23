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

final class MapsGoogleGeocoder extends MapsBaseGeocoder {
	
	/**
	 * @see MapsBaseGeocoder::geocode()
	 *
	 * @param string $address
	 */
	public static function geocode($address) {
		global $egGoogleMapsKey;

		// In case the google maps api key is not set, return false
		if (empty($egGoogleMapsKey)) return false;

		// Create the request url
		$requestURL = 'http://maps.google.com/maps/geo?q='.urlencode($address).'&output=csv&key='.urlencode($egGoogleMapsKey);

		//Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $requestURL);
		curl_setopt($ch, CURLOPT_HEADER, 0); //Change this to a 1 to return headers
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 
		$result = curl_exec($ch);
		curl_close($ch);
		
		//Check the Google Geocoder API Response code to ensure success
		if (substr($result, 0, 3) == "200") {
			$result =  explode(",", $result);
			
			//$precision = $result[1];
			$latitude = $result[2];
			$longitude = $result[3];

			return array(
						'lat' => $latitude,
						'lon' => $longitude
						);			
		}
		else { // When the request fails, return false
			return false;	
		}
	}
}