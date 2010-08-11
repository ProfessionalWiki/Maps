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
	 * Gets the contents of the first XML tag with the provided name,
	 * returns false when no matching element is found.
	 *
	 * @param string $xml
	 * @param string $tagName
	 * 
	 * @return string or false
	 */
	protected static function getXmlElementValue( $xml, $tagName ) {
		$match = array();
		preg_match( "/<$tagName>(.*?)<\/$tagName>/", $xml, $match );
		return count( $match ) > 1 ? $match[1] : false;
	}
	
}