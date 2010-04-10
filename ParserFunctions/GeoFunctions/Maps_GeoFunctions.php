<?php

/**
 * This file contains registration for the geographical coordinate functions
 * such as #geodistance for the Maps extension. 
 * 
 * @file Maps_GeoFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Pnelnik
 * @author Matěj Grabovský
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

// The approximate radius of the earth in km.
define( 'Maps_EARTH_RADIUS', 20000 / M_PI );

$wgHooks['LanguageGetMagic'][] = 'efMapsGeoFunctionsMagic';
$wgHooks['ParserFirstCallInit'][] = 'efMapsGeoFunctions';

/**
 * Adds the magic words for the parser functions.
 */
function efMapsGeoFunctionsMagic( &$magicWords, $langCode ) {
	$magicWords['geodistance'] = array( 0, 'geodistance' );
	
	return true; // Unless we return true, other parser functions won't get loaded.
}

/**
 * Adds the parser function hooks.
 */
function efMapsGeoFunctions( &$wgParser ) {
	// Hooks to enable the geocoding parser functions.
	$wgParser->setFunctionHook( 'geodistance', array( 'MapsGeoFunctions', 'renderGeoDistance' ) );
	
	return true;
}

final class MapsGeoFunctions {
	
	// TODO: add support for smart geocoding
	// TODO: add coordinate validation
	function renderGeoDistance( Parser &$parser ) {
		$args = func_get_args();
		
		// We already know the $parser.
		array_shift( $args ); 
	
		// Default parameter assignment, to allow for nameless syntax.
		$defaultParams = array( 'location1', 'location2' );
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
			'location1' => array(
				'required' => true
			),
			'location2' => array(
				'required' => true 
			),							
		);
		
		$manager = new ValidatorManager();
		
		$parameters = $manager->manageMapparameters( $parameters, $parameterInfo );
		
		$doCalculation = $parameters !== false;	
		
		if ( $doCalculation ) {
			if ( self::geocoderIsAvailable() ) {
				$start = MapsGeocoder::attemptToGeocode( $parameters['location1'] );
				$end = MapsGeocoder::attemptToGeocode( $parameters['location2'] );
			} else {
				$start = MapsCoordinateParser::parseCoordinates( $parameters['location1'] );
				$end = MapsCoordinateParser::parseCoordinates( $parameters['location2'] );				
			}		
			
			if ( $start && $end ) {
				$northRad1 = deg2rad( $start['lat'] );
				$eastRad1 = deg2rad( $start['lon'] );
		
				$cosNorth1 = cos( $northRad1 );
				$cosEast1 = cos( $eastRad1 );
				
				$sinNorth1 = sin( $northRad1 );
				$sinEast1 = sin( $eastRad1 );
				
				$northRad2 = deg2rad( $end['lat'] );
				$eastRad2 = deg2rad( $end['lon'] );		
				
				$cosNorth2 = cos( $northRad2 );
				$cosEast2 = cos( $eastRad2 );
		
				$sinNorth2 = sin( $northRad2 );
				$sinEast2 = sin( $eastRad2 );
			 
				$term1 = $cosNorth1 * $sinEast1 - $cosNorth2 * $sinEast2;
				$term2 = $cosNorth1 * $cosEast1 - $cosNorth2 * $cosEast2;
				$term3 = $sinNorth1 - $sinNorth2;
			 
				$distThruSquared = $term1 * $term1 + $term2 * $term2 + $term3 * $term3;
			 
				$surfaceDistance = 2 * Maps_EARTH_RADIUS * asin( sqrt( $distThruSquared ) / 2 );
				
				$output = $surfaceDistance . ' km';
				$errorList = $manager->getErrorList();	
				
				if ( $errorList != '' ) {
					$output .= '<br />' . $errorList;
				}
			} else {
				$errorList = '';
				
				if ( !$start ) {
					$errorList .= wfMsgExt( 'maps-invalid-coordinates', array( 'parsemag' ), $parameters['location1'] );
				}
				
				if ( !$end ) {
					if ( $errorList != '' ) $errorList .= '<br />'; 
					$errorList .= wfMsgExt( 'maps-invalid-coordinates', array( 'parsemag' ), $parameters['location2'] );
				}					
				
				$output = $errorList;
			}
		} else {
			// One of the parameters is not provided, so display an error message.
			// If the error level is Validator_ERRORS_MINIMAL, show the Validator_ERRORS_WARN message since 
			// the function could not do any work, otherwise use the error level as it is.
			global $egValidatorErrorLevel;
			$output = $manager->getErrorList( $egValidatorErrorLevel == Validator_ERRORS_MINIMAL ? Validator_ERRORS_WARN : $egValidatorErrorLevel );
		}
	 
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	
	/**
	 * Returns a boolean indicating if MapsGeocoder is available. 
	 */
	private static function geocoderIsAvailable() {
		global $wgAutoloadClasses;
		return array_key_exists( 'MapsGeocoder', $wgAutoloadClasses );
	}	
	
}

