<?php

/**
 * Initialization class for geocoder functionality of the Maps extension. 
 * 
 * @file Maps_Geocoders.php
 * @ingroup Maps
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
		
		$geoDir = dirname( __FILE__ ) . '/Geocoders/';
		// TODO: replace by autoloading
		include_once $geoDir . 'Maps_GoogleGeocoder.php'; 		// Google
		include_once $geoDir . 'Maps_YahooGeocoder.php'; 		// Yahoo!
		include_once $geoDir . 'Maps_GeonamesGeocoder.php'; 	// GeoNames

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