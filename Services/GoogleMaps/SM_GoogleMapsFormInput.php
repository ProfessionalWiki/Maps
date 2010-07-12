<?php

/**
 * File holding the SMGoogleMapsFormInput class.
 *
 * @file SM_GoogleMapsFormInput.php
 * @ingroup SMGoogleMaps
 *
 * @author Jeroen De Dauw
 * @author Robert Buzink
 * @author Yaron Koren
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for Google Maps v2 form inputs.
 * 
 * @ingroup SMGoogleMaps
 * 
 * @author Jeroen De Dauw
 * @author Robert Buzink
 * @author Yaron Koren
 */
class SMGoogleMapsFormInput extends SMFormInput {

	/**
	 * @see MapsMapFeature::setMapSettings()
	 */
	protected function setMapSettings() {
		global $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;
		$this->showAddresFunction = 'showGAddress';

		$this->earthZoom = 1;
	}
	
	/**
	 * @see smw/extensions/SemanticMaps/FormInputs/SMFormInput#addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgGoogleFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;

		$this->service->addDependencies( $wgOut );
		
		if ( empty( $smgGoogleFormsOnThisPage ) ) {
			$smgGoogleFormsOnThisPage = 0;
			$wgOut->addScriptFile( "$smgScriptPath/Services/GoogleMaps/SM_GoogleMapsFunctions{$egMapsJsExt}?$smgStyleVersion" );
		}
	}
	
	/**
	 * @see MapsMapFeature::addSpecificFormInputHTML
	 */
	protected function addSpecificMapHTML() {
		global $wgOut;
		
		$mapName = $this->service->getMapId( false );
		
		// Remove the overlays control in case it's present.
		// TODO: make less insane
		if ( in_string( 'overlays', $this->controls ) ) {
			$this->controls = str_replace( array( ",'overlays'", "'overlays'," ), '', $this->controls );
		}
		
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
		makeGoogleMapFormInput(
			'$mapName', 
			'$this->coordsFieldName',
			{
				lat: $this->centreLat,
				lon: $this->centreLon,
				zoom: $this->zoom,
				type: $this->type,
				types: [$this->types],
				controls: [$this->controls],
				scrollWheelZoom: $this->autozoom,
				kml: [$this->kml]
			},
			{$this->markerCoords['lat']},
			{$this->markerCoords['lon']}	
		);
	}	
);
EOT
		);
	}
	
	/**
	 * @see SMFormInput::manageGeocoding
	 */
	protected function manageGeocoding() {
		global $egGoogleMapsKey;
		return $egGoogleMapsKey != '';
	}
	
}