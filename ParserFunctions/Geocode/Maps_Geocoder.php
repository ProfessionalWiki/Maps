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

$wgHooks['LanguageGetMagic'][] = 'efMapsGeoFunctionMagic';
$wgHooks['ParserFirstCallInit'][] = 'efMapsRegisterGeoFunctions';

/**
 * Adds the magic words for the parser functions
 */
function efMapsGeoFunctionMagic( &$magicWords, $langCode ) {
	$magicWords['geocode'] = array( 0, 'geocode' );
	$magicWords['geocodelat']	= array ( 0, 'geocodelat' );
	$magicWords['geocodelng']	= array ( 0, 'geocodelng' );
	
	return true; // Unless we return true, other parser functions won't get loaded
}	

/**
 * Adds the parser function hooks
 */
function efMapsRegisterGeoFunctions(&$wgParser) {
	// Hooks to enable the geocoding parser functions
	$wgParser->setFunctionHook( 'geocode', array('MapsGeocoder', 'renderGeocoder') );
	$wgParser->setFunctionHook( 'geocodelat', array('MapsGeocoder', 'renderGeocoderLat') );
	$wgParser->setFunctionHook( 'geocodelng', array('MapsGeocoder', 'renderGeocoderLng') );
	
	return true;
}	

final class MapsGeocoder {

	/**
	 * Holds if geocoded data should be cached or not.
	 *
	 * @var boolean
	 */
	private static $mEnableCache = true;
	
	/**
	 * The geocoder cache, holding geocoded data when enabled.
	 *
	 * @var array
	 */
	private static $mGeocoderCache = array();

	/**
	 * Handler for the geocode parser function. Returns the latitude and longitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param unknown_type $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * @return string
	 */
	public static function renderGeocoder($parser, $address, $service = '', $mappingService = '') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? $geovalues['lat'].', '.$geovalues['lon'] : '';
	}

	/**
	 * Handler for the geocode parser function. Returns the latitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param unknown_type $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * @return string
	 */	
	public static function renderGeocoderLat(&$parser, $address, $service = '', $mappingService = '') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? $geovalues['lat'] : '';
	}
	
	/**
	 * Handler for the geocode parser function. Returns the longitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param unknown_type $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * @return string
	 */	
	public static function renderGeocoderLng(&$parser, $address, $service = '', $mappingService = '') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? $geovalues['lon'] : '';
	}
	
	/**
	 * Geocodes an address with the provided geocoding service and returns the result 
	 * as a string with the optionally provided format, or false when the geocoding failed.
	 * 
	 * @param string $address
	 * @param string $service
	 * @param string $mappingService
	 * @param string $format
	 * @return formatted coordinate string or false
	 */
	public static function geocodeToString($address, $service = '', $mappingService = '', $format = '%1$s, %2$s') {
		$geovalues = MapsGeocoder::geocode($address, $service, $mappingService);
		return $geovalues ? sprintf($format, $geovalues['lat'], $geovalues['lon']) : false;
	}

	/**
	 * Geocodes an address with the provided geocoding service and returns the result 
	 * as an array, or false when the geocoding failed.
	 *
	 * @param string $address
	 * @param string $service
	 * @param string $mappingService
	 * @return array with coordinates or false
	 */
	private static function geocode($address, $service, $mappingService) {
		global $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
		
		// If the adress is already in the cache and the cache is enabled, return the coordinates
		if (self::$mEnableCache && array_key_exists($address, MapsGeocoder::$mGeocoderCache)) {
			return self::$mGeocoderCache[$address];
		}
		
		$coordinates = false;
		
		$service = self::getValidGeoService($service, $mappingService);
		
		// If not, use the selected geocoding service to geocode the provided adress
		switch(strtolower($service)) {
			case 'google':			
				self::addAutoloadClassIfNeeded('MapsGoogleGeocoder', 'Maps_GoogleGeocoder.php');
				$coordinates = MapsGoogleGeocoder::geocode($address);
				break;			
			case 'yahoo':
				self::addAutoloadClassIfNeeded('MapsYahooGeocoder', 'Maps_YahooGeocoder.php');
				$coordinates = MapsYahooGeocoder::geocode($address);
				break;
			case 'geonames':
				self::addAutoloadClassIfNeeded('MapsGeonamesGeocoder', 'Maps_GeonamesGeocoder.php');
				$coordinates = MapsGeonamesGeocoder::geocode($address);
				break;
		}

		// Add the obtained coordinates to the cache when there is a result and the cache is enabled
		if (self::$mEnableCache && $coordinates) {
			MapsGeocoder::$mGeocoderCache[$address] = $coordinates;
		}

		return $coordinates;
	}

	/**
	 * Add a geocoder class to the $wgAutoloadClasses array when it's not present yet.
	 *
	 * @param string $className
	 * @param string $fileName
	 */
	private static function addAutoloadClassIfNeeded($className, $fileName) {
		global $wgAutoloadClasses, $egMapsIP;
		if (!array_key_exists($className, $wgAutoloadClasses)) $wgAutoloadClasses[$className] = $egMapsIP . '/Geocoders/' . $fileName;
	}
	
	/**
	 * Makes sure that the geo service is one of the available ones.
	 * Also enforces licencing restrictions when no geocoding service is explicitly provided.
	 *
	 * @param string $service
	 * @param string $mappingService
	 * @return string
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



