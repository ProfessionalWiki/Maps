<?php

/**
 * Yahoo! Geocoding Service info: http://developer.yahoo.com/geo/geoplanet/
 *
 * @file Maps_YahooGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * 
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsYahooGeocoder extends MapsBaseGeocoder {
	
	/**
	 * @see MapsBaseGeocoder::geocode()
	 *
	 * @param string $address
	 */
	public static function geocode($address) {
		global $egYahooMapsKey;

		// In case the Yahoo! Maps API key is not set, return false
		if (empty($egYahooMapsKey)) return false;

		// Create the request url
		$requestURL = "http://where.yahooapis.com/v1/places.q('".urlencode($address)."')?appid=".urlencode($egYahooMapsKey)."&format=xml"; 

		//Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $requestURL);
		curl_setopt($ch, CURLOPT_HEADER, 0); //Change this to a 1 to return headers
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 
		$result = curl_exec($ch);
		curl_close($ch);
	
		$lon = self::getXmlElementValue($result, "longitude");
		$lat = self::getXmlElementValue($result, "latitude");

		// In case one of the values is not found, return false
		if (!$lon || !$lat) return false;

		return array(
					'lat' => $lat,
					'lon' => $lon
					);	
	}	
	
}