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

// The approximate radius of the earth in meters, according to http://en.wikipedia.org/wiki/Earth_radius.
define( 'Maps_EARTH_RADIUS', 6371000 );

if ( version_compare( $wgVersion, '1.16alpha', '<' ) ) {
	$wgHooks['LanguageGetMagic'][] = 'efMapsGeoFunctionsMagic';
}
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
		global $egMapsDistanceUnit, $egMapsDistanceDecimals;
		
		$args = func_get_args();
		
		// We already know the $parser.
		array_shift( $args );
		
		$manager = new ValidatorManager();
		
		$doCalculation = $manager->manageParameters(
			$args,
			array(
				'location1' => array(
					'required' => true
				),
				'location2' => array(
					'required' => true
				),
				'unit' => array(
					'criteria' => array(
						'in_array' => MapsDistanceParser::getUnits()
					),
					'default' => $egMapsDistanceUnit
				),
				'decimals' => array(
					'type' => 'integer',
					'default' => $egMapsDistanceDecimals
				)				
			),
			array( 'location1', 'location2', 'unit', 'decimals' )
		);
		
		if ( $doCalculation ) {
			$parameters = $manager->getParameters( false );
			
			$canGeocode = MapsMapper::geocoderIsAvailable();
			
			if ( $canGeocode ) {
				$start = MapsGeocoder::attemptToGeocode( $parameters['location1'] );
				$end = MapsGeocoder::attemptToGeocode( $parameters['location2'] );
			} else {
				$start = MapsCoordinateParser::parseCoordinates( $parameters['location1'] );
				$end = MapsCoordinateParser::parseCoordinates( $parameters['location2'] );
			}
			
			if ( $start && $end ) {
				$output = MapsDistanceParser::formatDistance( self::calculateDistance( $start, $end ), $parameters['unit'], $parameters['decimals'] );
				$errorList = $manager->getErrorList();
				
				if ( $errorList != '' ) {
					$output .= '<br />' . $errorList;
				}
			} else {
				global $egValidatorFatalLevel;
				
				$fails = array();
				if ( !$start ) $fails[] = $parameters['location1'];
				if ( !$end ) $fails[] = $parameters['location2'];
				
				switch ( $egValidatorFatalLevel ) {
					case Validator_ERRORS_NONE:
						$output = '';
						break;
					case Validator_ERRORS_WARN:
						$output = '<b>' . htmlspecialchars( wfMsgExt( 'validator_warning_parameters', array( 'parsemag' ), count( $fails ) ) ) . '</b>';
						break;
					case Validator_ERRORS_SHOW: default:
						global $wgLang;
						
						if ( $canGeocode ) {
							$output = htmlspecialchars( wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), $wgLang->listToText( $fails ), count( $fails ) ) );
						} else {
							$output = htmlspecialchars( wfMsgExt( 'maps_unrecognized_coords', array( 'parsemag' ), $wgLang->listToText( $fails ), count( $fails ) ) );
						}
						break;
				}
			}
		} else {
			// One of the parameters is not provided, so display an error message.
			$output = $manager->getErrorList();
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
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation, $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;
		
		$args = func_get_args();
		
		// We already know the $parser.
		array_shift( $args );
		
		$manager = new ValidatorManager();
		
		$doCalculation = $manager->manageParameters(
			$args,
			array(
				'location' => array(
					'required' => true
				),
				'bearing' => array(
					'type' => 'float',
					'required' => true
				),
				'distance' => array(
					'type' => 'float',
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
			array( 'location', 'bearing', 'distance' )
		);

		if ( $doCalculation ) {
			$parameters = $manager->getParameters( false );
			
			$canGeocode = MapsMapper::geocoderIsAvailable();
			
			if ( $canGeocode ) {
				$location = MapsGeocoder::attemptToGeocode( $parameters['location'] );
			} else {
				$location = MapsCoordinateParser::parseCoordinates( $parameters['location'] );
			}
			
			if ( $location ) {
				$destination = self::findDestination(
					$location,
					$parameters['bearing'],
					MapsDistanceParser::parseDistance( $parameters['distance'] )
				);
				$output = MapsCoordinateParser::formatCoordinates( $destination, $parameters['format'], $parameters['directional'] );
			} else {
				global $egValidatorFatalLevel;
				switch ( $egValidatorFatalLevel ) {
					case Validator_ERRORS_NONE:
						$output = '';
						break;
					case Validator_ERRORS_WARN:
						$output = '<b>' . htmlspecialchars( wfMsgExt( 'validator_warning_parameters', array( 'parsemag' ), 1 ) ) . '</b>';
						break;
					case Validator_ERRORS_SHOW: default:
						// Show an error that the location could not be geocoded or the coordinates where not recognized.
						if ( $canGeocode ) {
							$output = htmlspecialchars( wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), $parameters['location'] ) );
						} else {
							$output = htmlspecialchars( wfMsgExt( 'maps-invalid-coordinates', array( 'parsemag' ), $parameters['location'] ) );
						}
						break;
				}
			}
		} else {
			// Either required parameters are missing, or there are errors while having a strict error level.
			$output = $manager->getErrorList();
		}
		
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	
	/**
	 * Returns the geographical distance between two coordinates.
	 * See http://en.wikipedia.org/wiki/Geographical_distance
	 * 
	 * @param array $start The first coordinates, as non-directional floats in an array with lat and lon keys.
	 * @param array $end The second coordinates, as non-directional floats in an array with lat and lon keys.
	 * 
	 * @return float Distance in m.
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
		$startingCoordinates['lat'] = deg2rad( (float)$startingCoordinates['lat'] );
		$startingCoordinates['lon'] = deg2rad( (float)$startingCoordinates['lon'] );
	
		$radBearing = deg2rad ( (float)$bearing );
		$angularDistance = $distance / Maps_EARTH_RADIUS;
		
		$lat = asin (sin ( $startingCoordinates['lat'] ) * cos ( $angularDistance ) + cos ( $startingCoordinates['lat'] )  * sin ( $angularDistance ) * cos ( $radBearing ) );
		$lon = $startingCoordinates['lon'] + atan2 ( sin ( $radBearing ) * sin ( $angularDistance ) * cos ( $startingCoordinates['lat'] ), cos ( $angularDistance ) - sin ( $startingCoordinates['lat'] ) * sin ( $lat ) );
	
		return array(
			'lat' => rad2deg( $lat ),
			'lon' => rad2deg( $lon )
		);
	}
	
}