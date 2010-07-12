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
	 * @see MapsMapFeature::setMapSettings()
	 */
	protected function setMapSettings() {
		global $egMapsYahooMapsPrefix;
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
		$this->showAddresFunction = 'showYAddress';

		$this->earthZoom = 17;
	}
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgYahooFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		$this->service->addDependencies( $wgOut );
		
		if ( empty( $smgYahooFormsOnThisPage ) ) {
			$smgYahooFormsOnThisPage = 0;
			
			$wgOut->addScriptFile( "$smgScriptPath/Services/YahooMaps/SM_YahooMapsFunctions{$egMapsJsExt}?$smgStyleVersion" );
		}
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML
	 * 
	 * TODO: fix map name
	 */
	protected function addSpecificMapHTML() {
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
			'$mapName',
			'$this->coordsFieldName',
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
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 */
	protected function manageGeocoding() {
		global $egYahooMapsKey;
		return $egYahooMapsKey != '';
	}

}