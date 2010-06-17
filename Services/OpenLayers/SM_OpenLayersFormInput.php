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
	
	protected $specificParameters = array();
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 */
	protected function setMapSettings() {
		global $egMapsOpenLayersZoom, $egMapsOpenLayersPrefix;
		
		$this->elementNamePrefix = $egMapsOpenLayersPrefix;

		$this->earthZoom = 1;

        $this->defaultZoom = $egMapsOpenLayersZoom;
	}
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgOLFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		$this->mService->addDependencies( $wgOut );
		
		if ( empty( $smgOLFormsOnThisPage ) ) {
			$smgOLFormsOnThisPage = 0;
			
			$wgOut->addScriptFile( "$smgScriptPath/Services/OpenLayers/SM_OpenLayersFunctions{$egMapsJsExt}?$smgStyleVersion" );
		}
	}
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 */
	protected function doMapServiceLoad() {
		global $egOpenLayersOnThisPage, $smgOLFormsOnThisPage, $egMapsOpenLayersPrefix;
		
		self::addFormDependencies();
		
		$egOpenLayersOnThisPage++;
		$smgOLFormsOnThisPage++;

		$this->elementNr = $egOpenLayersOnThisPage;
		$this->mapName = $egMapsOpenLayersPrefix . '_' . $egOpenLayersOnThisPage;
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML()
	 */
	protected function addSpecificMapHTML() {
		global $wgOut;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$layerItems = $this->mService->createLayersStringAndLoadDependencies( $this->layers );
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		makeFormInputOpenLayer(
			'$this->mapName',
			'$this->coordsFieldName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			{$this->markerCoords['lat']},
			{$this->markerCoords['lon']},
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