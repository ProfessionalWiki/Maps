<?php

/**
 * Class for handling the display_point(s) parser functions with OSM.
 *
 * @file Maps_OSMDispPoint.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsOSMDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsOSMUtils::SERVICE_NAME;	
	
	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsOSMZoom, $egMapsOSMPrefix;
		
		$this->defaultParams = MapsOSMUtils::getDefaultParams();
		
		$this->elementNamePrefix = $egMapsOSMPrefix;
		$this->defaultZoom = $egMapsOSMZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egOSMMapsOnThisPage;
		
		MapsOSMUtils::addOSMDependencies($this->output);
		$egOSMMapsOnThisPage++;
		
		$this->elementNr = $egOSMMapsOnThisPage;
	}	
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$markerItems = array();		
		
		// TODO: Refactor up
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];
			
			$title = array_key_exists('title', $markerData) ? $markerData['title'] : $this->title;
			$label = array_key_exists('label', $markerData) ? $markerData['label'] : $this->label;	
			
			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);				

			$icon = array_key_exists('icon', $markerData) ? $markerData['icon'] : '';
			$markerItems[] = "getOLMarkerData($lon, $lat, '$title', '$label', '$icon')";
		}		
		
		$markersString = implode(',', $markerItems);		
		
		$this->output .= "<div id='$this->mapName' style='width: {$this->width}px; height: {$this->height}px; background-color: #cccccc;'></div>
		<script type='$wgJsMimeType'> /*<![CDATA[*/
			addOnloadHook(
				initOpenLayer('$this->mapName', $this->centre_lon, $this->centre_lat, $this->zoom, [$layerItems], [$controlItems],[$markersString])
			);
		/*]]>*/ </script>";
	}

}