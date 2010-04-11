<?php

/**
 * This file contains registration for the #coordinates parser function,
 * which can transform the notation of a set of coordinates.
 * 
 * @file Maps_Coordinates.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgHooks['LanguageGetMagic'][] = 'efMapsCoordinatesMagic';
$wgHooks['ParserFirstCallInit'][] = 'efMapsCoordinatesFunction';

/**
 * Adds the magic words for the parser functions.
 */
function efMapsCoordinatesMagic( &$magicWords, $langCode ) {
	$magicWords['coordinates'] = array( 0, 'coordinates' );
	
	return true; // Unless we return true, other parser functions won't get loaded.
}

/**
 * Adds the parser function hooks.
 */
function efMapsCoordinatesFunction( &$wgParser ) {
	// Hooks to enable the geocoding parser functions.
	$wgParser->setFunctionHook( 'coordinates', 'efMapsRenderCoordinates' );
	
	return true;
}

// TODO: add coordinate validation
function efMapsRenderCoordinates() {
	global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsAvailableCoordNotations;
	global $egMapsDefaultServices, $egMapsDefaultGeoService, $egMapsCoordinateNotation;
	global $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;
	
	$args = func_get_args();
	
	// We already know the $parser.
	array_shift( $args ); 
	
	// For backward compatibility with pre 0.6.
	$defaultParams = array( 'location', 'format', 'directional' );
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
		'format' => array(
			'criteria' => array(
				'in_array' => $egMapsAvailableCoordNotations
			),
			'aliases' => array(
				'notation'
			),
			'default' => $egMapsCoordinateNotation
		),
		'directional' => array(
			'type' => 'boolean',
			'default' => $egMapsCoordinateDirectional
		),								
	);
	
	$manager = new ValidatorManager();
	
	$parameters = $manager->manageMapparameters( $parameters, $parameterInfo );
	
	$doFormatting = $parameters !== false;
	
	if ( $doFormatting ) {
		$parsedCoords = MapsCoordinateParser::parseCoordinates( $parameters['location'] );
		
		if ( $parsedCoords ) {
			$output = MapsCoordinateParser::formatCoordinates( $parsedCoords, $parameters['notation'], $parameters['directional'] ) .
				'<br />' . $manager->getErrorList();
		} else {
			$output = htmlspecialchars( wfMsgExt( 'maps-invalid-coordinates', 'parsemag', $parameters['location'] ) ) .
				'<br />' . $manager->getErrorList();
		}
	} else {
		$output = $manager->getErrorList();
	}

	return array( $output, 'noparse' => true, 'isHTML' => true );
}