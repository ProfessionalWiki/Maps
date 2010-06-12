<?php

/**
 * This groupe contains all Yahoo! Maps related files of the Maps extension.
 * 
 * @defgroup MapsYahooMaps Yahoo! Maps
 * @ingroup Maps
 */

/**
 * This file holds the hook and initialization for the Yahoo! Maps service. 
 *
 * @file YahooMaps.php
 * @ingroup MapsYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

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