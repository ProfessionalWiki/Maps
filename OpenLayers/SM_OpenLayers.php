<?php

/**
 * A query printer for maps using the Open Layers API
 *
 * @file SM_OpenLayers.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMOpenLayers extends SMMapPrinter {

	public function getName() {
		wfLoadExtensionMessages('SemanticMaps');
		return wfMsg('sm_openlayers_printername');
	}
	
	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 *
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsOpenLayersZoom;
		
		$this->elementNamePrefix = 'open_layer';
		$this->defaultZoom = $egMapsOpenLayersZoom;		
	}	

	/**
	 * @see SMMapPrinter::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egOpenLayersOnThisPage;
		
		MapsOpenLayers::addOLDependencies($this->output);
		$egOpenLayersOnThisPage++;
		
		$this->elementNr = $egOpenLayersOnThisPage;		
	}
	
	/**
	 * @see SMMapPrinter::addSpecificMapHTML()
	 *
	 */
	protected function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$controlItems = MapsOpenLayers::createControlsString($this->controls);
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies($this->output, $this->layers);

		MapsUtils::makePxValue($this->width);
		MapsUtils::makePxValue($this->height);
			
		$markerItems = array();
		
		foreach ($this->m_locations as $location) {
			// Create a string containing the marker JS 
			list($lat, $lon, $title, $label, $icon) = $location;
			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);
			$markerItems[] = "getOLMarkerData($lon, $lat, '$title', '$label')";
		}
		
		$markersString = implode(',', $markerItems);		
		
		$this->output .= "<div id='$this->mapName' style='width: $this->width; height: $this->height; background-color: #cccccc;'></div>
		<script type='$wgJsMimeType'> /*<![CDATA[*/
			addLoadEvent(
				initOpenLayer('$this->mapName', $this->centre_lon, $this->centre_lat, $this->zoom, [$layerItems], [$controlItems], [$markersString])
			);
		/*]]>*/ </script>";		
	}

}
