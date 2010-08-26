<?php

/**
 * This file contains registration for the #distance parser function,
 * which can convert a distance in one unit to the equivalent in another unit.
 * 
 * @file Maps_Distance.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( version_compare( $wgVersion, '1.16alpha', '<' ) ) {
	$wgHooks['LanguageGetMagic'][] = 'efMapsDistanceMagic';
}
$wgHooks['ParserFirstCallInit'][] = 'efMapsDistanceFunction';

/**
 * Adds the magic words for the parser functions.
 */
function efMapsDistanceMagic( &$magicWords, $langCode ) {
	$magicWords['distance'] = array( 0, 'distance' );
	
	return true; // Unless we return true, other parser functions won't get loaded.
}

/**
 * Adds the parser function hooks.
 */
function efMapsDistanceFunction( &$wgParser ) {
	// Hooks to enable the geocoding parser functions.
	$wgParser->setFunctionHook( 'distance', 'efMapsRenderDistance' );
	
	return true;
}

function efMapsRenderDistance() {
	global $egMapsDistanceUnit, $egMapsDistanceDecimals;
	
	$args = func_get_args();
	
	// We already know the $parser.
	array_shift( $args );
	
	$manager = new ValidatorManager();
	
	$doConversion = $manager->manageParameters(
		$args,
		array(
			'distance' => array(
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
		array( 'distance', 'unit', 'decimals' )
	);	
	
	if ( $doConversion ) {
		$parameters = $manager->getParameters( false );
		
		$distanceInMeters = MapsDistanceParser::parseDistance( $parameters['distance'] );
		
		$errorList = $manager->getErrorList();
		
		if ( $distanceInMeters ) {
			$output = MapsDistanceParser::formatDistance( $distanceInMeters, $parameters['unit'], $parameters['decimals'] ) . $errorList;
		} else {
			$output = $errorList . wfMsgExt( 'maps_invalid_distance', 'parsemag', '<b>' . $parameters['distance'] . '</b>' );
		}
	} else {
		$output = $manager->getErrorList();
	}

	return array( $output );
}