<?php

/**
 * This groupe contains all OpenLayers related files of the Maps extension.
 * 
 * @defgroup MapsOpenLayers OpenLayers
 * @ingroup Maps
 */

/**
 * This file holds the hook and initialization for the OpenLayers service. 
 *
 * @file OpenLayers.php
 * @ingroup MapsOpenLayers
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgHooks['MappingServiceLoad'][] = 'efMapsInitOpenLayers';

function efMapsInitOpenLayers() {
	global $egMapsServices, $wgAutoloadClasses;
	
	$wgAutoloadClasses['MapsOpenLayers'] = dirname( __FILE__ ) . '/Maps_OpenLayers.php';
	$wgAutoloadClasses['MapsOpenLayersDispMap'] = dirname( __FILE__ ) . '/Maps_OpenLayersDispMap.php';
	$wgAutoloadClasses['MapsOpenLayersDispPoint'] = dirname( __FILE__ ) . '/Maps_OpenLayersDispPoint.php';	
	
	$openLayers = new MapsOpenLayers();
	$openLayers->addFeature( 'display_point', 'MapsOpenLayersDispPoint' );
	$openLayers->addFeature( 'display_map', 'MapsOpenLayersDispMap' );

	$egMapsServices[$openLayers->getName()] = $openLayers;
	
	return true;
}