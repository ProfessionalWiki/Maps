<?php

/**
 * This group contains all Leaflet related files of the Semantic Maps extension.
 * 
 * @defgroup SMLeaflet Leaflet
 * @ingroup SMLeaflet
 */

/**
 * This file holds the general information for the Leaflet service.
 *
 * @since 1.0
 *
 * @file SM_Leaflet.php
 * @ingroup SMLeaflet
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >, Bernhard Krabina < krabina@kdz.or.at>
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$moduleTemplate = array(
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'SemanticMaps/src/services/Leaflet',
	'group' => 'ext.semanticmaps',
);

/* forms input with leaflet not implemented yet
$GLOBALS['wgResourceModules']['ext.sm.fi.leaflet'] = $moduleTemplate + array(
	'dependencies' => array(
		'ext.sm.fi.leaflet.single',
	),
	'scripts' => array(
		'ext.sm.leafletinput.js',
	),
);

$GLOBALS['wgResourceModules']['ext.sm.fi.leaflet.single'] = $moduleTemplate + array(
	'dependencies' => array(
		'ext.maps.leaflet',
		'ext.sm.forminputs',
	),
	'scripts' => array(
		'jquery.leafletinput.js',
	),
	'messages' => array(
	)
);
*/
unset( $moduleTemplate );

$GLOBALS['wgHooks']['MappingServiceLoad'][] = 'smfInitLeaflet';

function smfInitLeaflet() {
	global $wgAutoloadClasses;

	/* forms input with leaflet not implemented yet
	$wgAutoloadClasses['SMLeafletFormInput'] = __DIR__ . '/SM_LeafletFormInput.php';
	*/

	MapsMappingServices::registerServiceFeature( 'leaflet', 'qp', 'SMMapPrinter' );
	/* forms input with leaflet not implemented yet
	MapsMappingServices::registerServiceFeature( 'leaflet', 'fi', 'SMLeafletFormInput' );
	*/
	
	return true;
}
