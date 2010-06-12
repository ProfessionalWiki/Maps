<?php

$wgHooks['MappingServiceLoad'][] = 'efMapsInitGoogleMaps';

function efMapsInitGoogleMaps() {
	global $egMapsServices, $wgAutoloadClasses;
	
	$wgAutoloadClasses['MapsGoogleMaps'] = dirname( __FILE__ ) . '/Maps_GoogleMaps.php';
	$wgAutoloadClasses['MapsGoogleMapsDispMap'] = dirname( __FILE__ ) . '/Maps_GoogleMapsDispMap.php';
	$wgAutoloadClasses['MapsGoogleMapsDispPoint'] = dirname( __FILE__ ) . '/Maps_GoogleMapsDispPoint.php';	
	
	$googleMaps = new MapsGoogleMaps();
	$googleMaps->addFeature( 'display_point', 'MapsGoogleMapsDispPoint' );
	$googleMaps->addFeature( 'display_map', 'MapsGoogleMapsDispMap' );

	$egMapsServices[$googleMaps->getName()] = $googleMaps;
	
	return true;
}