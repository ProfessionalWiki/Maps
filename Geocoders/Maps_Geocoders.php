<?php

/**
 * Initialization file for geocoder functionality in the Maps extension.
 *
 * @file Maps_Geocoders.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsGeocoders'] = __FILE__;

$wgHooks['MappingFeatureLoad'][] = 'MapsGeocoders::initialize';

/**
 * Initialization class for geocoder functionality in the Maps extension. 
 * 
 * @author Jeroen De Dauw
 */
final class MapsGeocoders {
	
	/**
	 * Initialization function for Maps geocoder functionality.
	 */
	public static function initialize() {
		global $wgAutoloadClasses, $egMapsDir, $egMapsGeoServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsGeoOverrides;
		
		$egMapsGeoServices = array();
		$egMapsGeoOverrides = array();
		
		$wgAutoloadClasses['MapsBaseGeocoder'] 		= dirname( __FILE__ ) . '/Maps_BaseGeocoder.php';
		$wgAutoloadClasses['MapsGeocoder'] 			= dirname( __FILE__ ) . '/Maps_Geocoder.php';
		
		include_once dirname( __FILE__ ) . '/Maps_GoogleGeocoder.php'; 		// Google
		include_once dirname( __FILE__ ) . '/Maps_YahooGeocoder.php'; 		// Yahoo!
		include_once dirname( __FILE__ ) . '/Maps_GeonamesGeocoder.php'; 	// GeoNames

		// Remove the supported geocoding services that are not in the $egMapsAvailableGeoServices array.
		$supportedServices = array_keys( $egMapsGeoServices );
		foreach ( $supportedServices as $supportedService ) {
			if ( !in_array( $supportedService, $egMapsAvailableGeoServices ) ) {
				unset( $egMapsGeoServices[$supportedService] );
			}
		}
		
		// Re-populate the $egMapsAvailableGeoServices with it's original services that are supported. 
		$egMapsAvailableGeoServices = array_keys( $egMapsGeoServices );
		
		// Enure that the default geoservice is one of the enabled ones.
		if ( !in_array( $egMapsDefaultGeoService, $egMapsAvailableGeoServices ) ) {
			reset( $egMapsAvailableGeoServices );
			$egMapsDefaultGeoService = key( $egMapsAvailableGeoServices );
		}
		
		return true;
	}
	
}