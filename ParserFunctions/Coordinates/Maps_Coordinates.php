<?php

/**
 * This file contains registration 
 * 
 * 
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

function efMapsRenderCoordinates() {
	global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsAvailableCoordNotations;
	global $egMapsDefaultServices, $egMapsDefaultGeoService, $egMapsCoordinateNotation;
	global $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;
	
	$args = func_get_args();
	
	// We already know the $parser.
	array_shift( $args ); 
	
	// For backward compatibility with pre 0.6.
	$defaultParams = array( 'location', 'notation', 'directional' );
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
		'notation' => array(
			'criteria' => array(
				'in_array' => $egMapsAvailableCoordNotations
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
			return MapsCoordinateParser::formatCoordinates( $parsedCoords, $parameters['notation'], $parameters['directional'] );
		} else {
			return htmlspecialchars( wfMsgExt( 'maps-invalid-coordinates', 'parsemag', $parameters['location'] ) );
		}
	} else {
		return $manager->getErrorList();
	}		
}