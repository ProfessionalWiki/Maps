<?php

/**
 * File holding the SMOpenLayersFormInput class.
 *
 * @file SM_OpenLayersFormInput.php
 * @ingroup SMOpenLayers
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for OpenLayers form inputs.
 * 
 * @ingroup SMOpenLayers
 * 
 * @author Jeroen De Dauw
 */
class SMOpenLayersFormInput extends SMFormInput {
	
	/**
	 * @see SMFormInput::getEarthZoom
	 * 
	 * @since 0.6.5
	 */
	protected function getEarthZoom() {
		return 1;
	}	
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgOLFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		$this->service->addDependencies( $wgOut );
		
		if ( empty( $smgOLFormsOnThisPage ) ) {
			$smgOLFormsOnThisPage = 0;
			
			$wgOut->addScriptFile( "$smgScriptPath/Services/OpenLayers/SM_OpenLayersFunctions{$egMapsJsExt}?$smgStyleVersion" );
		}
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML
	 */
	public function addSpecificMapHTML() {
		global $wgOut, $wgLang;
		
		$mapName = $this->service->getMapId( false );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$layerItems = $this->service->createLayersStringAndLoadDependencies( $this->layers );
		
		$langCode = $wgLang->getCode();
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		makeFormInputOpenLayer(
			'$mapName',
			'$this->coordsFieldName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			{$this->markerCoords['lat']},
			{$this->markerCoords['lon']},
			[$layerItems],
			[$this->controls],
			'$langCode'
		);
	}
);
EOT
		);
		
	}
	
}