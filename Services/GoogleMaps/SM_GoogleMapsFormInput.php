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
	
	protected $spesificParameters = array();
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 *
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
		global $wgJsMimeType;
		global $smgScriptPath, $smgGoogleFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		MapsGoogleMaps::addGMapDependencies( $this->output );
		
		if ( empty( $smgGoogleFormsOnThisPage ) ) {
			$smgGoogleFormsOnThisPage = 0;
			$this->output .= "<script type='$wgJsMimeType' src='$smgScriptPath/GoogleMaps/SM_GoogleMapsFunctions{$egMapsJsExt}?$smgStyleVersion'></script>";
		}
	}
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage, $smgGoogleFormsOnThisPage;
		
		self::addFormDependencies();
		
		$egGoogleMapsOnThisPage++;
		$smgGoogleFormsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsMapFeature::addSpecificFormInputHTML()
	 *
	 */
	protected function addSpecificMapHTML( Parser $parser ) {
		global $wgOut;
		
		// Remove the overlays control in case it's present.
		if ( in_string( 'overlays', $this->controls ) ) {
			$this->controls = str_replace( ",'overlays'", '', $this->controls );
			$this->controls = str_replace( "'overlays',", '', $this->controls );
		}
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			wfMsg('maps-loading-map')
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
			$this->marker_lat,
			$this->marker_lon	
		);
	}	
);
EOT
		);
	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {
		global $egGoogleMapsKey;
		$this->enableGeocoding = strlen( trim( $egGoogleMapsKey ) ) > 0;
		if ( $this->enableGeocoding ) MapsGoogleMaps::addGMapDependencies( $this->output );
	}
	
}
