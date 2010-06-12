<?php

$wgHooks['MappingServiceLoad'][] = 'efMapsInitYahooMaps';

function efMapsInitYahooMaps() {
	global $egMapsServices, $wgAutoloadClasses;
	
	$wgAutoloadClasses['MapsYahooMaps'] = dirname( __FILE__ ) . '/Maps_YahooMaps.php';
	$wgAutoloadClasses['MapsYahooMapsDispMap'] = dirname( __FILE__ ) . '/Maps_YahooMapsDispMap.php';
	$wgAutoloadClasses['MapsYahooMapsDispPoint'] = dirname( __FILE__ ) . '/Maps_YahooMapsDispPoint.php';	
	
	$yahooMaps = new MapsYahooMaps();
	$yahooMaps->addFeature( 'display_point', 'MapsYahooMapsDispPoint' );
	$yahooMaps->addFeature( 'display_map', 'MapsYahooMapsDispMap' );

	$egMapsServices[$yahooMaps->getName()] = $yahooMaps;
	
	return true;
}