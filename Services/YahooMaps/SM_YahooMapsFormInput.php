<?php

/**
 * File holding the SMYahooMapsFormInput class.
 *
 * @file SM_YahooMapsFormInput.php
 * @ingroup SMYahooMaps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for Yahoo Maps! form inputs.
 * 
 * @ingroup SMYahooMaps
 * 
 * @author Jeroen De Dauw
 */
class SMYahooMapsFormInput extends SMFormInput {
	
	/**
	 * @see SMFormInput::getEarthZoom
	 * 
	 * @since 0.6.5
	 */
	protected function getEarthZoom() {
		return 17;
	}	
	
	/**
	 * @see SMFormInput::getShowAddressFunction
	 * 
	 * @since 0.6.5
	 */	
	protected function getShowAddressFunction() {
		global $egYahooMapsKey;
		return $egYahooMapsKey == '' ? false : 'showYAddress';	
	}	
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgYahooFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		$this->service->addDependency( Html::linkedScript( "$smgScriptPath/Services/YahooMaps/SM_YahooMapsForms{$egMapsJsExt}?$smgStyleVersion" ) );
		$this->service->addDependencies( $wgOut );
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML
	 */
	public function addSpecificMapHTML() {
		global $wgOut;
		
		$mapName = $this->service->getMapId( false );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		makeFormInputYahooMap(
			"$mapName",
			"$this->coordsFieldName",
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
			{$this->markerCoords['lat']},
			{$this->markerCoords['lon']}
		);
	}
);
EOT
		);

	}
	
}