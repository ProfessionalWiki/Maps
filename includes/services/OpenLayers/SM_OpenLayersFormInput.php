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
		global $smgScriptPath, $smgOLFormsOnThisPage, $smgStyleVersion;
		
		$this->service->addDependency( Html::linkedScript( "$smgScriptPath/includes/services/OpenLayers/SM_OpenLayersForms.js?$smgStyleVersion" ) );
		$this->service->addDependencies( $wgOut );
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML
	 */
	public function addSpecificMapHTML() {
		global $wgLang;
		
		$mapName = $this->service->getMapId( false );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$this->service->addLayerDependencies( $this->layers[1] );
		
		$langCode = $wgLang->getCode();
		
		MapsMapper::addInlineScript( $this->service,<<<EOT
		makeFormInputOpenLayer(
			"$mapName",
			"$this->coordsFieldName",
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			{$this->markerCoords['lat']},
			{$this->markerCoords['lon']},
			{$this->layers[0]},
			[$this->controls],
			"$langCode"
		);
EOT
		);
		
	}
	
}