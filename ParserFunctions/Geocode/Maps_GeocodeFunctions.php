<?php

/**
 * This file contains the registration functions for the following parser functions: 
 *
 * @file Maps_GeocodeFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Sergey Chernyshev
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgHooks['LanguageGetMagic'][] = 'efMapsGeoFunctionMagic';
$wgHooks['ParserFirstCallInit'][] = 'efMapsRegisterGeoFunctions';

/**
 * Adds the magic words for the parser functions.
 */
function efMapsGeoFunctionMagic( &$magicWords, $langCode ) {
	$magicWords['geocode'] = array( 0, 'geocode' );
	$magicWords['geocodelat']	= array ( 0, 'geocodelat' );
	$magicWords['geocodelon']	= array ( 0, 'geocodelon', 'geocodelng' );
	
	return true; // Unless we return true, other parser functions won't get loaded.
}

/**
 * Adds the parser function hooks.
 */
function efMapsRegisterGeoFunctions( &$wgParser ) {
	// Hooks to enable the geocoding parser functions.
	$wgParser->setFunctionHook( 'geocode', array( 'MapsGeocodeFunctions', 'renderGeocoder' ) );
	$wgParser->setFunctionHook( 'geocodelat', array( 'MapsGeocodeFunctions', 'renderGeocoderLat' ) );
	$wgParser->setFunctionHook( 'geocodelon', array( 'MapsGeocodeFunctions', 'renderGeocoderLon' ) );
	
	return true;
}

final class MapsGeocodeFunctions {

	/**
	 * Returns a boolean indicating if MapsGeocoder is available. 
	 */
	private static function geocoderIsAvailable() {
		global $wgAutoloadClasses;
		return array_key_exists( 'MapsGeocoder', $wgAutoloadClasses );
	}
	
	/**
	 * Handler for the geocode parser function. Returns the latitude and longitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param $parser
	 * @param string $coordsOrAddress The address to geocode, or coordinates to reformat.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * 
	 * TODO: rewrite
	 * 
	 * @return string
	 */
	public static function renderGeocoder( Parser $parser ) {
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsAvailableCoordNotations;
		global $egMapsDefaultServices, $egMapsDefaultGeoService, $egMapsCoordinateNotation;
		global $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;
		
		$args = func_get_args();
		
		// We already know the $parser.
		array_shift( $args ); 
		
		// For backward compatibility with pre 0.6.
		$defaultParams = array( 'location', 'service', 'mappingservice' );
		$parameters = array();
		
		// Determine all parameter names and value, and take care of default (nameless)
		// parameters, by turning them into named ones.
		foreach( $args as $arg ) {
			$parts = explode( '=', $arg );
			if ( count( $parts ) == 1 ) {
				if ( count( $defaultParams ) > 0 ) {
					$defaultParam = array_shift( $defaultParams ); 
					$parameters[$defaultParam] = trim( $parts[0] );	
				}
			} else {
				$name = strtolower( trim( array_shift( $parts ) ) );
				$parameters[$name] = trim( implode( $parts ) );
			}
		}
		
		$parameterInfo = array(
			'location' => array(
				'required' => true 
			),
			'mappingservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableServices
				),
				'default' => false
			),
			'service' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
			'format' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableCoordNotations
				),
				'aliases' => array(
					'notation'
				),				
				'default' => $egMapsCoordinateNotation
			),
			'allowcoordinates' => array(
				'type' => 'boolean',
				'default' => $egMapsAllowCoordsGeocoding
			),
			'directional' => array(
				'type' => 'boolean',
				'default' => $egMapsCoordinateDirectional
			),								
		);
		
		$manager = new ValidatorManager();
		
		$parameters = $manager->manageMapparameters( $parameters, $parameterInfo );
		
		$doGeocoding = $parameters !== false;
		
		if ( $doGeocoding ) {
			if ( self::geocoderIsAvailable() ) {
				$geovalues = MapsGeocoder::attemptToGeocodeToString( 
					$parameters['location'],
					$parameters['service'],
					$parameters['mappingservice'],
					$parameters['allowcoordinates'],
					$parameters['notation'],
					$parameters['directional']
				);
				return ( $geovalues ? $geovalues : '' ) . $manager->getErrorList();
			}
			else {
				return htmlspecialchars( wfMsg( 'maps-geocoder-not-available' ) );
			}
		} else {
			return $manager->getErrorList();
		}				
	}

	/**
	 * Handler for the geocode parser function. Returns the latitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * 
	 * @return string
	 */
	public static function renderGeocoderLat( Parser &$parser, $address, $service = '', $mappingService = '' ) {
		if ( self::geocoderIsAvailable() ) $geovalues = MapsGeocoder::geocode( $address, $service, $mappingService );
		return $geovalues ? $geovalues['lat'] : '';
	}
	
	/**
	 * Handler for the geocode parser function. Returns the longitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * 
	 * @return string
	 */
	public static function renderGeocoderLon( Parser &$parser, $address, $service = '', $mappingService = '' ) {
		if ( self::geocoderIsAvailable() ) $geovalues = MapsGeocoder::geocode( $address, $service, $mappingService );
		return $geovalues ? $geovalues['lon'] : '';
	}
	
}



