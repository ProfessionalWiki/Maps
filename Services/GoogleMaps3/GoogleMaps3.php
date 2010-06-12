<?php

/**
 * This groupe contains all Google Maps v3 related files of the Maps extension.
 * 
 * @defgroup MapsGoogleMaps3 Google Maps v3
 * @ingroup Maps
 */

/**
 * This file holds the hook and initialization for the Google Maps v3 service. 
 *
 * @file GoogleMaps3.php
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgHooks['MappingServiceLoad'][] = 'efMapsInitGoogleMaps3';

function efMapsInitGoogleMaps3() {
	global $egMapsServices, $wgAutoloadClasses;
	
	$wgAutoloadClasses['MapsGoogleMaps3'] = dirname( __FILE__ ) . '/Maps_GoogleMaps3.php';
	$wgAutoloadClasses['MapsGoogleMaps3DispMap'] = dirname( __FILE__ ) . '/Maps_GoogleMaps3DispMap.php';
	
	$googleMaps = new MapsGoogleMaps3();
	$googleMaps->addFeature( 'display_map', 'MapsGoogleMaps3DispMap' );

	$egMapsServices[$googleMaps->getName()] = $googleMaps;
	
	return true;
}