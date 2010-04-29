<?php

/**
 * File holding the registration and handling functions for the display_map parser function.
 *
 * @file Maps_DisplayMap.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsDisplayMap'] 		= __FILE__;
$wgAutoloadClasses['MapsBaseMap'] 			= dirname( __FILE__ ) . '/Maps_BaseMap.php';

if ( version_compare( $wgVersion, '1.16alpha', '<' ) ) {
	$wgHooks['LanguageGetMagic'][] = 'efMapsDisplayMapMagic';
}
$wgHooks['ParserFirstCallInit'][] 			= 'efMapsRegisterDisplayMap';

$egMapsFeatures['pf'][]	= 'MapsDisplayMap::initialize';

/**
 * Adds the magic words for the parser functions.
 */
function efMapsDisplayMapMagic( &$magicWords, $langCode ) {
	$magicWords['display_map'] = array( 0,  'display_map' );
	
	return true; // Unless we return true, other parser functions won't get loaded.
}

/**
 * Adds the parser function hooks
 */
function efMapsRegisterDisplayMap( &$wgParser ) {
	// A hook to enable the '#display_map' parser function.
	$wgParser->setFunctionHook( 'display_map', array( 'MapsDisplayMap', 'displayMapRender' ) );
	
	return true;
}

/**
 * Class containing the rendering functions for the display_map parser function.
 * 
 * @author Jeroen De Dauw
 *
 */
final class MapsDisplayMap {
	
	public static $parameters = array();
	
	public static function initialize() {
	}
	
	/**
	 * Returns the output for a display_map call.
	 *
	 * @param unknown_type $parser
	 * 
	 * @return array
	 */
	public static function displayMapRender( &$parser ) {
		$args = func_get_args();
		return MapsParserFunctions::getMapHtml( $parser, $args, 'display_map' );
	}
	
}