<?php

/**
 * This groupe contains all Google Maps v3 related files of the Maps extension.
 * 
 * @defgroup MapsGoogleMaps3 Google Maps v3
 * @ingroup Maps
 */

/**
 * This file holds the general information for the Google Maps v3 service.
 *
 * @file Maps_GoogleMaps3.php
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['googlemaps3'] = array(
									'pf' => array(
										'display_point' => array('class' => 'MapsGoogleMaps3DispPoint', 'file' => 'GoogleMaps/Maps_GoogleMaps3DispPoint.php', 'local' => true),
										'display_map' => array('class' => 'MapsGoogleMaps3DispMap', 'file' => 'GoogleMaps/Maps_GoogleMaps3DispMap.php', 'local' => true),
										),
									'classes' => array(
											array('class' => 'MapsGoogleMaps3', 'file' => 'GoogleMaps/Maps_GoogleMaps3.php', 'local' => true)
											),
									'aliases' => array('google3', 'googlemap3', 'gmap3', 'gmaps3'),
									);	

/**
 * Class for Google Maps v3 initialization.
 * 
 * @ingroup MapsGoogleMaps3
 * 
 * @author Jeroen De Dauw
 */											
class MapsGoogleMaps {
	
	const SERVICE_NAME = 'googlemaps3';	
	
	public static function initialize() {
		self::initializeParams();
	}
	
	private static function initializeParams() {
		global $egMapsServices;
		
		$egMapsServices[self::SERVICE_NAME]['parameters'] = array(
				);
	}
	
	/**
	 * Add references to the Google Maps API v3 and required JS file to the provided output 
	 *
	 * @param string $output
	 */
	public static function addGMap3Dependencies(&$output) {
		global $wgJsMimeType, $wgLang;
		global $egMapsScriptPath, $egGoogleMaps3OnThisPage, $egMapsStyleVersion;

		if (empty($egGoogleMaps3OnThisPage)) {
			$egGoogleMaps3OnThisPage = 0;

			$output .= "<script type='$wgJsMimeType' src='http://maps.google.com/maps/api/js?sensor=false&language={$wgLang->getCode()}'><script type='$wgJsMimeType' src='$egMapsScriptPath/GoogleMaps3/GoogleMap3Functions.js?$egMapsStyleVersion'></script>";
		}
	}
	
}
									