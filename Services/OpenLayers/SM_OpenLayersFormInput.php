<?php

/**
 * Form input hook that adds an Open Layers map format to Semantic Forms
 *
 * @file SM_OpenLayersFormInput.php
 * @ingroup SMOpenLayers
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMOpenLayersFormInput extends SMFormInput {
	
	public $serviceName = MapsOpenLayers::SERVICE_NAME;
	
	protected $spesificParameters = array();
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsOpenLayersZoom, $egMapsOpenLayersPrefix;
		
		$this->elementNamePrefix = $egMapsOpenLayersPrefix;

		$this->earthZoom = 1;

        $this->defaultZoom = $egMapsOpenLayersZoom;
	}
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 * 	  
	 */
	protected function addFormDependencies() {
		global $wgJsMimeType;
		global $smgScriptPath, $smgOLFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		MapsOpenLayers::addOLDependencies( $this->output );
		
		if ( empty( $smgOLFormsOnThisPage ) ) {
			$smgOLFormsOnThisPage = 0;
			$this->output .= "<script type='$wgJsMimeType' src='$smgScriptPath/OpenLayers/SM_OpenLayersFunctions{$egMapsJsExt}?$smgStyleVersion'></script>";
		}
	}
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egOpenLayersOnThisPage, $smgOLFormsOnThisPage;
		
		self::addFormDependencies();
		
		$egOpenLayersOnThisPage++;
		$smgOLFormsOnThisPage++;

		$this->elementNr = $egOpenLayersOnThisPage;
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML()
	 *
	 */
	protected function addSpecificMapHTML( Parser $parser ) {
		global $wgOut;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			wfMsg('maps-loading-map')
		);
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies( $this->output, $this->layers );
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		makeFormInputOpenLayer(
			'$this->mapName',
			'$this->coordsFieldName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->marker_lat,
			$this->marker_lon,
			[$layerItems],
			[$this->controls]
		);
	}
);
EOT
		);
		
	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 * TODO: find a geocoding service that can be used here
	 */
	protected function manageGeocoding() {
		$this->enableGeocoding = false;
	}
	
}
