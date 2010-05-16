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

if ( version_compare( $wgVersion, '1.16alpha', '<' ) ) {
	$wgHooks['LanguageGetMagic'][] = 'efMapsGeoFunctionMagic';
}
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
	 * @param Parser $parser
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
		
		$manager = new ValidatorManager();
		
$doGeocoding = $manager->manageParameters(
	$args,
	array(
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
	),
	array( 'location', 'service', 'mappingservice' )
);

if ( $doGeocoding ) {
	$parameters = $manager->getParameters( false );
			
			if ( self::geocoderIsAvailable() ) {
				$geovalues = MapsGeocoder::attemptToGeocodeToString(
					$parameters['location'],
					$parameters['service'],
					$parameters['mappingservice'],
					$parameters['allowcoordinates'],
					$parameters['format'],
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
	 * @param Parser $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * 
	 * @return string
	 */
	public static function renderGeocoderLat( Parser &$parser, $address, $service = '', $mappingService = '' ) {
		if ( MapsMapper::geocoderIsAvailable() ) $geovalues = MapsGeocoder::geocode( $address, $service, $mappingService );
		return $geovalues ? $geovalues['lat'] : '';
	}
	
	/**
	 * Handler for the geocode parser function. Returns the longitude
	 * for the provided address, or an empty string, when the geocoding fails.
	 *
	 * @param Parser $parser
	 * @param string $address The address to geocode.
	 * @param string $service Optional. The geocoding service to use.
	 * @param string $mappingService Optional. The mapping service that will use the geocoded data.
	 * 
	 * @return string
	 */
	public static function renderGeocoderLon( Parser &$parser, $address, $service = '', $mappingService = '' ) {
		if ( MapsMapper::geocoderIsAvailable() ) $geovalues = MapsGeocoder::geocode( $address, $service, $mappingService );
		return $geovalues ? $geovalues['lon'] : '';
	}
	
}