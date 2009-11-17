<?php

/**
 * File holding the MapsOpenLayersDispPoint class.
 *
 * @file Maps_OpenLayersDispPoint.php
 * @ingroup MapsOpenLayers
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}


/**
 * Class for handling the display_point(s) parser functions with OpenLayers.
 *
 * @author Jeroen De Dauw
 */
class MapsOpenLayersDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsOpenLayersUtils::SERVICE_NAME;	
	
	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsOpenLayersZoom, $egMapsOpenLayersPrefix;
		
		$this->defaultParams = MapsOpenLayersUtils::getDefaultParams();
		
		$this->elementNamePrefix = $egMapsOpenLayersPrefix;
		$this->defaultZoom = $egMapsOpenLayersZoom;
		
		$this->markerStringFormat = 'getOLMarkerData(lat, lon, "title", "label", "icon")';
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egOpenLayersOnThisPage;
		
		MapsOpenLayersUtils::addOLDependencies($this->output);
		$egOpenLayersOnThisPage++;
		
		$this->elementNr = $egOpenLayersOnThisPage;
	}	
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$controlItems = MapsOpenLayersUtils::createControlsString($this->controls);
		
		MapsMapper::enforceArrayValues($this->layers);
		$layerItems = MapsOpenLayersUtils::createLayersStringAndLoadDependencies($this->output, $this->layers);	
		
		$this->output .= "<div id='$this->mapName' style='width: {$this->width}px; height: {$this->height}px; background-color: #cccccc;'></div>
		<script type='$wgJsMimeType'> /*<![CDATA[*/
			addOnloadHook(
				initOpenLayer('$this->mapName', $this->centre_lon, $this->centre_lat, $this->zoom, [$layerItems], [$controlItems],[$this->markerString], $this->height)
			);
		/*]]>*/ </script>";
	}

}