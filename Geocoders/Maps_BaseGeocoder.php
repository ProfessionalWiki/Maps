<?php

/**
 * File holding the MapsBaseGeocoder class.
 *
 * @file Maps_BaseGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * MapsBaseGeocoder is an abstract class inherited by the geocoding classes.
 * 
 * @ingroup Maps
 * @since 0.2
 * 
 * @author Jeroen De Dauw
 */
abstract class MapsBaseGeocoder {
	
	/**
	 * Returns an array containing the geocoded latitude (lat) and
	 * longitude (lon) of the provided address, or false in case the
	 * geocoding fails.
	 *
	 * @since 0.2
	 *
	 * @param $address String: the address to be geocoded
	 * 
	 * @return string or false
	 */
	public static function geocode( $address ) {
		return false; // This method needs to be overriden, if it's not, return false.
	}
	
	/**
	 * Returns the content of the requested file, or false when the connection fails
	 * 
	 * @since 0.2
	 * 
	 * @param $requestURL String: the url to make the request to
	 * 
	 * @return string or false
	 */
	protected static function GetResponse( $requestURL ) {
		// Attempt to get CURL response
		$response = self::GetCurlResponse( $requestURL );
		
		// Attempt to get response using fopen when the CURL request failed
		if ( !$response ) $response = self::GetUrlResponse( $requestURL );
		
		return $response;
	}
	
	/**
	 * Attempts to get the contents of a file via cURL request and
	 * returns it, or false when the attempt fails.
	 * 
	 * @param $requestURL String: the url to make the request to
	 * 
	 * @return string or false
	 */
	protected static function GetCurlResponse( $requestURL ) {
		if ( function_exists( "curl_init" ) ) {
			try {
				// Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
				$ch = curl_init();
			
				curl_setopt( $ch, CURLOPT_URL, $requestURL );
				curl_setopt( $ch, CURLOPT_HEADER, 0 ); // Change this to a 1 to return headers
				if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) )
					curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
				// TODO else curl_setopt($ch, CURLOPT_USERAGENT, "MediaWiki/Maps extension");
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			 
				$result = curl_exec( $ch );
				curl_close( $ch );
				
				return $result;
			}
			catch ( Exception $ex ) {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	/**
	 * Attempts to get the contents of a file via fopen and
	 * returns it, or false when the attempt fails.
	 * 
	 * @param string $requestURL
	 * @return string or false
	 */
	protected static function GetUrlResponse( $requestURL ) {
		if ( function_exists( 'fopen' ) ) {
			try {
				if ( $handle = fopen( $requestURL, 'r' ) ) {
		            $result = fread( $handle, 10000 );
		            fclose( $handle );
		        }
		        else { // When the request fails, return false
		            $result = false;
		        }
			}
			catch ( Exception $ex ) {
				$result = false;
			}
		}
		else {
			$result = false;
		}
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
	protected static function getXmlElementValue( $xml, $tagName ) {
		$match = array();
		preg_match( "/<$tagName>(.*?)<\/$tagName>/", $xml, $match );
		return count( $match ) > 1 ? $match[1] : false;
	}
	
}