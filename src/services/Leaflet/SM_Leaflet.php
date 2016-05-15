<?php

/**
 * This file holds the general information for the Leaflet service.
 *
 * @licence GNU GPL v2+
 * @author Bernhard Krabina < krabina@kdz.or.at>
 * @author Peter Grassberger < petertheone@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$moduleTemplate = [
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'SemanticMaps/src/services/Leaflet',
	'group' => 'ext.semanticmaps',
];

$GLOBALS['wgResourceModules']['ext.sm.fi.leafletajax'] = $moduleTemplate + [
	'dependencies' => [
		'ext.maps.leaflet'
	],
	'scripts' => [
		'ext.sm.leafletajax.js'
	]
];

unset( $moduleTemplate );

$GLOBALS['wgHooks']['MappingServiceLoad'][] = 'smfInitLeaflet';

function smfInitLeaflet() {
	MapsMappingServices::registerServiceFeature( 'leaflet', 'qp', 'SMMapPrinter' );

	/* @var MapsMappingService $leaflet */
	$leaflet = MapsMappingServices::getServiceInstance( 'leaflet' );
	$leaflet->addResourceModules(array( 'ext.sm.fi.leafletajax' ));

	return true;
}
