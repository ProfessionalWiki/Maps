<?php
/**
 * A class that holds static helper functions for Open Players
 *
 * @file Maps_OpenLayers.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsOpenLayers extends MapsBaseMap {
	private static $loadedBing = false; 
	private static $loadedYahoo = false;
	private static $loadedOL = false;
	private static $loadedOSM = false; 
	
	/**
	 * Load the dependencies of a layer if they are not loaded yet
	 *
	 * @param unknown_type $output The output to which the html to load the dependencies needs to be added
	 * @param unknown_type $layer The layer to check (and load the dependencies for
	 * @param unknown_type $includePath The path to the extension directory
	 */
	public static function loadDependencyWhenNeeded(&$output, $layer) {
		global $loadedBing, $loadedYahoo, $loadedOL, $loadedOSM;
		global $wgJsMimeType;
		global $egGoogleMapsOnThisPage, $egMapsIncludePath;
		
		switch ($layer) {
			case 'google' : case 'google-normal' : case 'google-sattelite' : case 'google-hybrid' : case 'google-physical' :
				if (empty($egGoogleMapsOnThisPage)) {
					$egGoogleMapsOnThisPage = 0;
					MapsGoogleMaps::addGMapDependencies($output);
					}
				break;
			case 'bing' : case 'virtual-earth' :
				if (!$loadedBing) { $output .= "<script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>\n"; $loadedBing = true; }
				break;
			case 'yahoo' : case 'yahoo-maps' :
				if (!$loadedYahoo) { $output .= "<style type='text/css'> #controls {width: 512px;}</style><script src='http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers'></script>\n"; $loadedYahoo = true; }
				break;
			case 'openlayers' : case 'open-layers' :
				if (!$loadedOL) { $output .= "<script type='$wgJsMimeType' src='http://clients.multimap.com/API/maps/1.1/metacarta_04'></script>\n"; $loadedOL = true; }
				break;
			case 'osm' : case 'openstreetmap' :
				if (!$loadedOSM) { $output .= "<script type='$wgJsMimeType' src='$egMapsIncludePath/OpenLayers/OSM/OpenStreetMap.js'></script>\n"; $loadedOSM = true; }
				break;													
		}		
	}
	
	/**
	 * If this is the first open layers map on the page, load the API, styles and extra JS functions
	 * 
	 * @param unknown_type $output
	 */
	public static function addOLDependencies(&$output) {
		global $wgJsMimeType;
		global $egOpenLayersOnThisPage, $egMapsIncludePath;
		
		if (empty($egOpenLayersOnThisPage)) {
			$egOpenLayersOnThisPage = 0;
			
			$output .="<link rel='stylesheet' href='$egMapsIncludePath/OpenLayers/OpenLayers/theme/default/style.css' type='text/css' />
			<script type='$wgJsMimeType' src='$egMapsIncludePath/OpenLayers/OpenLayers/OpenLayers.js'></script>		
			<script type='$wgJsMimeType' src='$egMapsIncludePath/OpenLayers/OpenLayerFunctions.js'></script>
			<script type='$wgJsMimeType'>setOLPopupType(200, 100);</script>\n";
		}		
	}
		
	/**
	 * Build up a csv string with the layers, to be outputted as a JS array
	 *
	 * @param unknown_type $output
	 * @param unknown_type $layers
	 * @return csv string
	 */
	public static function createLayersStringAndLoadDependencies(&$output, $layers) {
		global $egMapsOLLayers;
		
		if (count($layers) < 1) $layers = $egMapsOLLayers;
		
		$layerItems = '';
		foreach ($layers as $layer) {
			$layer = strtolower($layer);
			$layerItems .= "'$layer'" . ',';
			MapsOpenLayers::loadDependencyWhenNeeded($output, $layer);
		}
		
		return rtrim($layerItems, ',');		
	}
	
	/**
	 * Build up a csv string with the controls, to be outputted as a JS array
	 *
	 * @param unknown_type $controls
	 * @return csv string
	 */
	public static function createControlsString($controls) {
		global $egMapsOLControls;
		return MapsMapper::createControlsString($controls, $egMapsOLControls);
	}		
	
	public static function displayMap(&$parser, $map) {
		global $egOpenLayersOnThisPage, $egMapsOpenLayersZoom;
		global $wgJsMimeType;
		
		$params = MapsMapper::setDefaultParValues($map, true);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item.
		foreach($params as $paramName => $paramValue) {
			if (empty(${$paramName})) ${$paramName} = $paramValue;
		}		
		
		if (strlen($zoom) < 1) $zoom = $egMapsOpenLayersZoom;
		
		$output = '';
		
		MapsOpenLayers::addOLDependencies($output);
		$egOpenLayersOnThisPage++;
		
		$controlItems = MapsOpenLayers::createControlsString($controls);
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies($output, $layers);

		$coordinates = str_replace('″', '"', $coordinates);
		$coordinates = str_replace('′', "'", $coordinates);

		list($lat, $lon) = MapsUtils::getLatLon($coordinates);

		$width = $width . 'px';
		$height = $height . 'px';		
		
		$output .="<div id='openlayer_$egOpenLayersOnThisPage' style='width: $width; height: $height; background-color: #cccccc;'></div><script type='$wgJsMimeType'>initOpenLayer('openlayer_$egOpenLayersOnThisPage', $lon, $lat, $zoom, [$layerItems], [$controlItems],[getOLMarkerData($lon, $lat, '', '')]);</script>";

        return $output;
	}

}