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
	 * @param unknown_type $address
	 */
	public static function geocode($address) {
		global $egYahooMapsKey;

		// In case the Yahoo! Maps API key is not set, return false
		if (empty($egYahooMapsKey)) return false;

		// Create the request url
		$requestURL = "http://where.yahooapis.com/v1/places.q('".urlencode($address)."')?appid=".urlencode($egYahooMapsKey)."&format=xml"; 

		if ($handle = fopen($requestURL, "r")) {
			$result = fread($handle, 10000);
			fclose($handle);
			
			$lon = self::getXmlElementValue($result, "longitude");
			$lat = self::getXmlElementValue($result, "latitude");

			// In case one of the values is not found, return false
			if (!$lon || !$lat) return false;

			return array(
						'lat' => $lat,
						'lon' => $lon
						);
		}
		else { // When the request fails, return false
			return false;
		}		
	}	
	
	/**
	 * Gets the contents of the first XML tag with the provided name,
	 * returns false when no matching element is found.
	 *
	 * @param string $xml
	 * @param string $tagName
	 * @return unknown
	 */
	private static function getXmlElementValue($xml, $tagName) {
		$match = array();
		preg_match("/<$tagName>(.*?)<\/$tagName>/", $xml, $match);
		return count($match) > 1 ? $match[1] : false;
	}
	
}