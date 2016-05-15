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
 * @author Peter Grassberger < petertheone@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$moduleTemplate = [
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'SemanticMaps/src/services/GoogleMaps3',
	'group' => 'ext.semanticmaps',
];

$GLOBALS['wgResourceModules']['ext.sm.fi.googlemaps3ajax'] = $moduleTemplate + [
	'dependencies' => [
		'ext.maps.googlemaps3'
	],
	'scripts' => [
		'ext.sm.googlemaps3ajax.js'
	]
];

$GLOBALS['wgResourceModules']['ext.sm.fi.googlemaps3'] = $moduleTemplate + [
	'dependencies' => [
		'ext.sm.fi.googlemaps3.single',
	],
	'scripts' => [
		'ext.sm.googlemapsinput.js',
	],
];

$GLOBALS['wgResourceModules']['ext.sm.fi.googlemaps3.single'] = $moduleTemplate + [
	'dependencies' => [
		'ext.maps.googlemaps3',
		'ext.sm.forminputs',
	],
	'scripts' => [
		'jquery.googlemapsinput.js',
	],
	'messages' => [
	]
];

unset( $moduleTemplate );

$GLOBALS['wgHooks']['MappingServiceLoad'][] = 'smfInitGoogleMaps3';

function smfInitGoogleMaps3() {
	global $wgAutoloadClasses;
	
	$wgAutoloadClasses['SMGoogleMaps3FormInput'] = __DIR__ . '/SM_GoogleMaps3FormInput.php';

	MapsMappingServices::registerServiceFeature( 'googlemaps3', 'qp', 'SMMapPrinter' );
	MapsMappingServices::registerServiceFeature( 'googlemaps3', 'fi', 'SMGoogleMaps3FormInput' );

	/* @var MapsMappingService $googleMaps */
	$googleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
	$googleMaps->addResourceModules(array( 'ext.sm.fi.googlemaps3ajax' ));

	return true;
}
