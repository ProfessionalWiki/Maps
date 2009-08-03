<?php

/**
 * Form input hook that adds an Open Layers map format to Semantic Forms
 *
 * @file SM_OpenLayersFormInput.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMOpenLayersFormInput extends SMFormInput {
	
	/**
	 * @see SMFormInput::setFormInputSettings()
	 *
	 */
	protected function setFormInputSettings() {
		global $egMapsOpenLayersZoom;
		
		$this->elementNamePrefix = 'open_layer';
		$this->showAddresFunction = 'showOLAddress';	

		$this->earthZoom = 1;
		$this->defaultZoom = $egMapsOpenLayersZoom;		
	}	
	
	/**
	 * @see SMFormInput::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egOpenLayersOnThisPage;
		
		MapsOpenLayers::addOLDependencies($this->formOutput);
		$egOpenLayersOnThisPage++;	

		$this->elementNr = $egOpenLayersOnThisPage;
	}	
	
	/**
	 * @see SMFormInput::addSpecificFormInputHTML()
	 *
	 */
	protected function addSpecificFormInputHTML() {
		global $wgJsMimeType;
		
		$controlItems = MapsOpenLayers::createControlsString($this->controls);
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies($this->formOutput, $this->layers);	
		
		$width = $this->width . 'px';
		$height = $this->height . 'px';			
		
		$this->formOutput .="
		<div id='".$this->mapName."' style='width: $width; height: $height; background-color: #cccccc;'></div>  
		
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputOpenLayer('".$this->mapName."', '".$this->coordsFieldName."', ".$this->centre_lat.", ".$this->centre_lon.", ".$this->zoom.", ".$this->marker_lat.", ".$this->marker_lon.", [$layerItems], [$controlItems]));
		/*]]>*/</script>";			
	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {	
		$this->enableGeocoding = false;
	}
	
}
