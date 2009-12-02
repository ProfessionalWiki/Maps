<?php

/**
 * This groupe contains all OpenStreetMap related files of the Maps extension.
 * 
 * @defgroup MapsOpenStreetMap OpenStreetMap
 * @ingroup Maps
 */

/**
 * This file holds the general information for the OSM optimized OpenLayers service
 *
 * @file Maps_OSM.php
 * @ingroup MapsOpenStreetMap
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['osm'] = array(
									'pf' => array(
										'display_point' => array('class' => 'MapsOSMDispPoint', 'file' => 'OpenStreetMap/Maps_OSMDispPoint.php', 'local' => true),
										'display_map' => array('class' => 'MapsOSMDispMap', 'file' => 'OpenStreetMap/Maps_OSMDispMap.php', 'local' => true),
										),
									'classes' => array(
											array('class' => 'MapsOSM', 'file' => 'OpenStreetMap/Maps_OSM.php', 'local' => true)
											),
									'aliases' => array('openstreetmap', 'openstreetmaps'),
									);
									
/**
 * Class for OpenStreetMap initialization.
 * 
 * @ingroup MapsOpenStreetMap
 * 
 * @author Jeroen De Dauw
 */						
class MapsOSM {
	
	const SERVICE_NAME = 'osm';		
	
	public static function initialize() {
		self::initializeParams();
	}
	
	private static function initializeParams() {
		global $wgLang;
		global $egMapsServices, $egMapsOSMZoom, $egMapsOSMControls;
		
		$egMapsServices[self::SERVICE_NAME]['parameters'] = array(
			'zoom' => array(
				'default' => $egMapsOSMZoom, 	
				),
			'controls' => array(
				'criteria' => array(), // TODO
				'default' => implode(',', $egMapsOSMControls)			
				),	
			'lang' => array(
				'aliases' => array('locale', 'language'),	
				'criteria' => array(
					'in_array' => array_keys( Language::getLanguageNames( false ) )
					),
				'default' => $wgLang->getCode()
				),												
			);
	}
	
	// TODO: create a modular system for this SlippyMap code
	
	private static $layers = array(
		'osm-wm' => array(
			// First layer = default
			'layers' => array( 'osm-like' ),
	
			// Default "zoom=" argument
			'defaultZoomLevel' => 14,
	
			'static_rendering' => array(
				'type' => 'SlippyMapExportCgiBin',
				'options' => array(
					'base_url' => 'http://cassini.toolserver.org/cgi-bin/export',
	
					'format' => 'png',
					'numZoomLevels' => 19,
					'maxResolution' => 156543.0339,
					'unit' => 'm',
					'sphericalMercator' => true,
	
					// More GET arguments
					'get_args' => array(
						// Will use $wgContLang->getCode()
						'locale' => true,
						'maptype' => 'osm-like'
					),
				),
			),
		),
		'osm' => array(
			// First layer = default
			'layers' => array( 'mapnik', 'osmarender', 'maplint', 'cycle' ),
	
			// Default "zoom=" argument
			'defaultZoomLevel' => 14,
	
			'static_rendering' => array(
				'type' => 'SlippyMapExportCgiBin',
				'options' => array(
					'base_url' => 'http://tile.openstreetmap.org/cgi-bin/export',
	
					'format' => 'png',
					'numZoomLevels' => 19,
					'maxResolution' => 156543.0339,
					'unit' => 'm',
					'sphericalMercator' => true
				),
			),
		),
		'satellite' => array(
			'layers' => array( 'urban', 'landsat', 'bluemarble' ),
			'defaultZoomLevel' => 14,
			'static_rendering' => null,
		),
	);	
	
	/**
	 * If this is the first OSM map on the page, load the OpenLayers API, OSM styles and extra JS functions
	 * 
	 * @param string $output
	 */
	public static function addOSMDependencies(&$output) {
		global $wgJsMimeType;
		global $egOSMMapsOnThisPage, $egMapsScriptPath, $egMapsStyleVersion;
		
		if (empty($egOSMMapsOnThisPage)) {
			$egOSMMapsOnThisPage = 0;
			
			$output .="<link rel='stylesheet' href='$egMapsScriptPath/OpenLayers/OpenLayers/theme/default/style.css' type='text/css' />
			<script type='$wgJsMimeType' src='$egMapsScriptPath/OpenLayers/OpenLayers/OpenLayers.js'></script>		
			<script type='$wgJsMimeType' src='$egMapsScriptPath/OpenStreetMap/OSMFunctions.js?$egMapsStyleVersion'></script>
			<script type='$wgJsMimeType'>slippymaps = Array();</script>\n";
		}		
	}			
	
}									