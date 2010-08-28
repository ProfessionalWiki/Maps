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

if ( version_compare( $wgVersion, '1.16alpha', '<' ) ) {
	$wgHooks['LanguageGetMagic'][] = 'efMapsCoordinatesMagic';
}
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
	global $egMapsAvailableServices, $egMapsAvailableCoordNotations;
	global $egMapsDefaultServices, $egMapsDefaultGeoService, $egMapsCoordinateNotation;
	global $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;	
	
	$args = func_get_args();
	
	// We already know the $parser.
	array_shift( $args );
	
	$manager = new ValidatorManager();
	
	$doFormatting = $manager->manageParameters(
		$args,
		array(
			'location' => array(
				'required' => true,
				'tolower' => false
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
		),
		array( 'location', 'format', 'directional' )
	);
	
	if ( $doFormatting ) {
		$parameters = $manager->getParameters( false );
		
		$parsedCoords = MapsCoordinateParser::parseCoordinates( $parameters['location'] );
		
		if ( $parsedCoords ) {
			$output = MapsCoordinateParser::formatCoordinates( $parsedCoords, $parameters['format'], $parameters['directional'] );
		} else {
			$output = htmlspecialchars( wfMsgExt( 'maps-invalid-coordinates', 'parsemag', $parameters['location'] ) );
		}
		
		$errorList = $manager->getErrorList();

		if ( $errorList != '' ) {
			$output .= '<br />' . $errorList;
		}
	} else {
		$output = $manager->getErrorList();
	}

	return array( $output );
}