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
	
}