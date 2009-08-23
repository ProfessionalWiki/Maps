<?php

/**
 * MapsBaseGeocoder is an abstract class inherited by the geocoding classes
 *
 * @file Maps_BaseGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

abstract class MapsBaseGeocoder {
	
	/**
	 * Returns an array containing the geocoded latitude (lat) and
	 * longitude (lon) of the provided address, or false in case the
	 * geocoding fails.
	 *
	 * @param string $address
	 */
	public abstract static function geocode($address);
	
	protected static function GetCurlResponse($requestURL) {
		//Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $requestURL);
		curl_setopt($ch, CURLOPT_HEADER, 0); //Change this to a 1 to return headers
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
	
	/**
	 * Gets the contents of the first XML tag with the provided name,
	 * returns false when no matching element is found.
	 *
	 * @param string $xml
	 * @param string $tagName
	 * @return string or false
	 */
	protected static function getXmlElementValue($xml, $tagName) {
		$match = array();
		preg_match("/<$tagName>(.*?)<\/$tagName>/", $xml, $match);
		return count($match) > 1 ? $match[1] : false;
	}
	
}
