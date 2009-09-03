<?php

/**
 * A class that holds static helper functions and extension hooks for the Google Maps service
 *
 * @file SM_GoogleMapsFormInput.php
 * @ingroup SemanticMaps
 * 
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMapsFormInput extends SMFormInput {

	public $serviceName = MapsGoogleMaps::SERVICE_NAME;
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;
		$this->showAddresFunction = 'showGAddress';	

		$this->earthZoom = 1;
		
		$this->defaultParams = MapsGoogleMapsUtils::getDefaultParams();
        $this->defaultZoom = $egMapsGoogleMapsZoom;		
	}
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 *
	 * @param string $output
	 */
	protected function addFormDependencies(&$output) {
		global $wgJsMimeType;
		global $smgIncludePath, $smgGoogleFormsOnThisPage;
		
		MapsGoogleMapsUtils::addGMapDependencies($output);
		
		if (empty($smgGoogleFormsOnThisPage)) {
			$smgGoogleFormsOnThisPage = 0;
			$output .= "<script type='$wgJsMimeType' src='$smgIncludePath/GoogleMaps/SM_GoogleMapFunctions.js'></script>";
		}
	}
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage, $smgGoogleFormsOnThisPage;
		
		self::addFormDependencies($this->output);
		
		$egGoogleMapsOnThisPage++;
		$smgGoogleFormsOnThisPage++;	
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsMapFeature::addSpecificFormInputHTML()
	 *
	 */
	protected function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$enableEarth = MapsGoogleMapsUtils::getEarthValue($this->earth);
		
		$this->autozoom = MapsGoogleMapsUtils::getAutozoomJSValue($this->autozoom);
		
		$this->type = MapsGoogleMapsUtils::getGMapType($this->type, true);
		
		$this->controls = MapsGoogleMapsUtils::createControlsString($this->controls);		
		
		$this->types = explode(",", $this->types);
		
		$typesString = MapsGoogleMapsUtils::createTypesString($this->types, $enableEarth);
		
		$this->output .= "
		<div id='".$this->mapName."' class='".$this->class."'></div>
	
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputGoogleMap('$this->mapName', '$this->coordsFieldName', $this->width, $this->height, $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$typesString], [$this->controls], $this->autozoom, $this->marker_lat, $this->marker_lon));
		window.unload = GUnload;
		/*]]>*/</script>";
	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {
		global $egGoogleMapsKey;
		$this->enableGeocoding = strlen(trim($egGoogleMapsKey)) > 0;
		if ($this->enableGeocoding) MapsGoogleMapsUtils::addGMapDependencies($this->output);		
	}	
	
}
