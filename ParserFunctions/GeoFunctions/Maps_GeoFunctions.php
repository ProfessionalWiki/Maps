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
	$magicWords['finddestination'] = array( 0, 'finddestination' );
	
	return true; // Unless we return true, other parser functions won't get loaded.
}

/**
 * Adds the parser function hooks.
 */
function efMapsGeoFunctions( &$wgParser ) {
	// Hooks to enable the geocoding parser functions.
	$wgParser->setFunctionHook( 'geodistance', array( 'MapsGeoFunctions', 'renderGeoDistance' ) );
	$wgParser->setFunctionHook( 'finddestination', array( 'MapsGeoFunctions', 'renderFindDestination' ) );
	
	return true;
}

final class MapsGeoFunctions {
	
	/**
	 * Handler for the #geodistance parser function. 
	 * See http://mapping.referata.com/wiki/Geodistance
	 * 
	 * @param Parser $parser
	 */
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
				$output = self::calculateDistance( $start, $end ) . ' km';
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
	 * Handler for the #finddestination parser function. 
	 * See http://mapping.referata.com/wiki/Finddestination
	 * 
	 * @param Parser $parser
	 */	
	public static function renderFindDestination( Parser &$parser ) {
		$args = func_get_args();
		
		// We already know the $parser.
		array_shift( $args ); 
	
		// Default parameter assignment, to allow for nameless syntax.
		$defaultParams = array( 'location', 'bearing', 'distance' );
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
			'bearing' => array(
				'required' => true 
			),
			'distance' => array(
				'required' => true 
			),						
		);
		
		$manager = new ValidatorManager();
		
		$parameters = $manager->manageMapparameters( $parameters, $parameterInfo );
		
		$doCalculation = $parameters !== false;	
				
		// TODO
	}
	
	/**
	 * Returns the geographical distance between two coordinates.
	 * See http://en.wikipedia.org/wiki/Geographical_distance
	 * 
	 * @param array $start The first coordinates, as non-directional floats in an array with lat and lon keys.
	 * @param array $end The second coordinates, as non-directional floats in an array with lat and lon keys.
	 * 
	 * @return float Distance in km.
	 */
	public static function calculateDistance( array $start, array $end ) {
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
	 
		return 2 * Maps_EARTH_RADIUS * asin( sqrt( $distThruSquared ) / 2 );		
	}
	
	/**
	 * Finds a destination given a starting location, bearing and distance.
	 * 
	 * @param array $startingCoordinates The starting coordinates, as non-directional floats in an array with lat and lon keys.
	 * @param float $bearing The initial bearing in degrees.
	 * @param float $distance The distance to travel in km.
	 * 
	 * @return array The desitination coordinates, as non-directional floats in an array with lat and lon keys.
	 */
	public static function findDestination( array $startingCoordinates, $bearing, $distance ) {
		$angularDistance = $distance / Maps_EARTH_RADIUS;
		$lat = asin(
				sin( $startingCoordinates['lat'] ) * cos( $angularDistance ) +
				cos( $startingCoordinates['lat'] ) * sin( $angularDistance ) * cos( $bearing )
		);
		return array(
			'lat' => $lat,
			'lon' => $startingCoordinates['lon'] + atan2(
				sin( $bearing ) * sin( $angularDistance ) * cos( $startingCoordinates['lat'] ),
				cos( $angularDistance ) - sin( $startingCoordinates['lat'] ) * sin( $lat )
			)
		);
	}
	
	/**
	 * Returns a boolean indicating if MapsGeocoder is available. 
	 * 
	 * @return Boolean
	 */
	private static function geocoderIsAvailable() {
		global $wgAutoloadClasses;
		return array_key_exists( 'MapsGeocoder', $wgAutoloadClasses );
	}	
	
}

