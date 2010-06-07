<?php

/**
 * A class that holds static helper functions and extension hooks for the Google Maps service
 *
 * @file SM_GoogleMapsFormInput.php
 * @ingroup SMGoogleMaps
 * 
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMapsFormInput extends SMFormInput {

	public $serviceName = MapsGoogleMaps::SERVICE_NAME;
	
	protected $specificParameters = array();
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 */
	protected function setMapSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;
		$this->showAddresFunction = 'showGAddress';

		$this->earthZoom = 1;
		
        $this->defaultZoom = $egMapsGoogleMapsZoom;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see smw/extensions/SemanticMaps/FormInputs/SMFormInput#addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgGoogleFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;

		MapsGoogleMaps::addGMapDependencies( $wgOut );
		
		if ( empty( $smgGoogleFormsOnThisPage ) ) {
			$smgGoogleFormsOnThisPage = 0;
			$wgOut->addScriptFile( "$smgScriptPath/Services/GoogleMaps/SM_GoogleMapsFunctions{$egMapsJsExt}?$smgStyleVersion" );
		}
	}
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 */
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage, $smgGoogleFormsOnThisPage, $egMapsGoogleMapsPrefix;
		
		self::addFormDependencies();
		
		$egGoogleMapsOnThisPage++;
		$smgGoogleFormsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
		$this->mapName = $egMapsGoogleMapsPrefix . '_' . $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsMapFeature::addSpecificFormInputHTML()
	 */
	protected function addSpecificMapHTML() {
		global $wgOut;
		
		// Remove the overlays control in case it's present.
		// TODO: make less insane
		if ( in_string( 'overlays', $this->controls ) ) {
			$this->controls = str_replace( array( ",'overlays'", "'overlays'," ), '', $this->controls );
		}
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		makeGoogleMapFormInput(
			'$this->mapName', 
			'$this->coordsFieldName',
			{
				lat: $this->centreLat,
				lon: $this->centreLon,
				zoom: $this->zoom,
				type: $this->type,
				types: [$this->types],
				controls: [$this->controls],
				scrollWheelZoom: $this->autozoom
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
	 * @see SMFormInput::manageGeocoding()
	 */
	protected function manageGeocoding() {
		global $egGoogleMapsKey, $wgParser;
		$this->enableGeocoding = $egGoogleMapsKey != '';
		if ( $this->enableGeocoding ) MapsGoogleMaps::addGMapDependencies( $wgParser );
	}
	
}