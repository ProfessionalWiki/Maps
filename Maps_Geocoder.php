<?php

/**
 * File containing the MapsGeocoder class which handles the non specific geocoding tasks
 *
 * {{#geocode:<Address>|<param1>=<value1>|<param2>=<value2>}}
 * {{#geocodelat:<Address>|<param1>=<value1>|<param2>=<value2>}}
 * {{#geocodelng:<Address>|<param1>=<value1>|<param2>=<value2>}}
 *
 * @file Maps_Geocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Sergey Chernyshev
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGeocoder {
	// TODO: some refactoring: the arrays containing the result should be generalized - currently only logical for the Google Geocoder service
	
	private static $mEnableCache = true;
	private static $mGeocoderCache = array();

	public static function renderGeocoder($parser, $address, $service = '', $mappingService = '') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? $geovalues['lat'].', '.$geovalues['lon'] : '';
	}

	public static function renderGeocoderLat(&$parser, $address, $service = '', $mappingService = '') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? $geovalues['lat'] : '';
	}

	public static function renderGeocoderLng(&$parser, $address, $service = '', $mappingService = '') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? $geovalues['lon'] : '';
	}

	/**
	 * Geocode an address with the provided geocoding service
	 *
	 * @param unknown_type $address
	 * @param unknown_type $service
	 * @return unknown
	 */
	private static function geocode($address, $service, $mappingService) {
		global $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
		
		// If the adress is already in the cache and the cache is enabled, return the coordinates
		if (self::$mEnableCache && array_key_exists($address, MapsGeocoder::$mGeocoderCache)) {
			return self::$mGeocoderCache[$address];
		}
		
		$service = self::getValidGeoService($service, $mappingService);

		// If not, use the selected geocoding service to geocode the provided adress
		switch(strtolower($service)) {
			case 'yahoo':
				self::addAutoloadClassIfNeeded('MapsYahooGeocoder', 'Maps_YahooGeocoder.php');
				$coordinates = MapsYahooGeocoder::geocode($address);
				break;
			default:
				self::addAutoloadClassIfNeeded('MapsGoogleGeocoder', 'Maps_GoogleGeocoder.php');
				$coordinates = MapsGoogleGeocoder::geocode($address);
				break;
		}

		// Add the obtained coordinates to the cache when there is a result and the cache is enabled
		if (self::$mEnableCache && isset($coordinates)) {
			MapsGeocoder::$mGeocoderCache[$address] = $coordinates;
		}

		return $coordinates;
	}

	private static function addAutoloadClassIfNeeded($className, $fileName) {
		global $wgAutoloadClasses, $egMapsIP;
		if (!array_key_exists($className, $wgAutoloadClasses)) $wgAutoloadClasses[$className] = $egMapsIP . '/Geocoders/' . $fileName;
	}
	
	/**
	 * Make sure that the geo service is one of the available
	 *
	 * @param unknown_type $service
	 * @return unknown
	 */
	private static function getValidGeoService($service, $mappingService) {
		global $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
		
		if (strlen($service) < 1) {
			
			// Set the default geocoding services.
			// Note that googlemaps and yahoomaps are excetions to the general rule due to licencing.
			switch ($mappingService) {
				case 'googlemaps' :
					$service = 'google';
					break;
				case 'yahoomaps' :
					$service = 'yahoo';					
					break;	
				default :
					$service = $egMapsDefaultGeoService;
					break;
			}
			
		}
		else {
			if(!in_array($service, $egMapsAvailableGeoServices)) $service = $egMapsDefaultGeoService;
		}

		return $service;
	}	
}



