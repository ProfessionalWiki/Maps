<?php

/**
 * This group contains all Google Maps v3 related files of the Semantic Maps extension.
 * 
 * @defgroup SMGoogleMaps3 Google Maps v3
 * @ingroup SMGoogleMaps3
 */

/**
 * This file holds the general information for the Google Maps v3 service.
 *
 * @since 1.0
 *
 * @file SM_GoogleMaps3.php
 * @ingroup SMGoogleMaps3
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$moduleTemplate = array(
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'SemanticMaps/src/services/GoogleMaps3',
	'group' => 'ext.semanticmaps',
);

$GLOBALS['wgResourceModules']['ext.sm.fi.googlemaps3'] = $moduleTemplate + array(
	'dependencies' => array(
		'ext.sm.fi.googlemaps3.single',
	),
	'scripts' => array(
		'ext.sm.googlemapsinput.js',
	),
);

$GLOBALS['wgResourceModules']['ext.sm.fi.googlemaps3.single'] = $moduleTemplate + array(
	'dependencies' => array(
		'ext.maps.googlemaps3',
		'ext.sm.forminputs',
	),
	'scripts' => array(
		'jquery.googlemapsinput.js',
	),
	'messages' => array(
	)
);

unset( $moduleTemplate );

$GLOBALS['wgHooks']['MappingServiceLoad'][] = 'smfInitGoogleMaps3';

function smfInitGoogleMaps3() {
	global $wgAutoloadClasses;
	
	$wgAutoloadClasses['SMGoogleMaps3FormInput'] = __DIR__ . '/SM_GoogleMaps3FormInput.php';

	MapsMappingServices::registerServiceFeature( 'googlemaps3', 'qp', 'SMMapPrinter' );
	MapsMappingServices::registerServiceFeature( 'googlemaps3', 'fi', 'SMGoogleMaps3FormInput' );

	return true;
}
