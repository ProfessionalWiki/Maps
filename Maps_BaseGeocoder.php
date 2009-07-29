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
	
	public abstract static function geocode($address);
	
}
