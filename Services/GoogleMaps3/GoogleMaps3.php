<?php

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