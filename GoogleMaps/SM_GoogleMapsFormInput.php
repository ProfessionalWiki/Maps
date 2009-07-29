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

	/**
	 * @see SMFormInput::setFormInputSettings()
	 *
	 */
	protected function setFormInputSettings() {
		global $egMapsGoogleMapsZoom;
		
		$this->elementNamePrefix = 'map_google';
		$this->showAddresFunction = 'showGAddress';	

		$this->earthZoom = 1;
		$this->defaultZoom = $egMapsGoogleMapsZoom;
	}
	

	/**
	 * @see SMFormInput::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;

		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies($this->formOutput);
		}
		
		$egGoogleMapsOnThisPage++;	
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see SMFormInput::addSpecificFormInputHTML()
	 *
	 */
	protected function addSpecificFormInputHTML() {
		global $wgJsMimeType;
		
		$enableEarth = $this->mapProperties['earth'] == 'on' || $this->mapProperties['earth'] == 'yes';
		$earth = $enableEarth ? 'true' : 'false';
		
		$this->type = MapsGoogleMaps::getGMapType($this->type, $enableEarth);
		$control = MapsGoogleMaps::getGControlType($this->controls);		
		
		$this->formOutput .= "
		<div id='".$this->mapName."' class='".$this->class."'></div>
	
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputGoogleMap('".$this->mapName."', '".$this->coordsFieldName."', ".$this->width.", ".$this->height.", ".$this->centre_lat.", ".$this->centre_lon.", ".$this->zoom.", ".$this->marker_lat.", ".$this->marker_lon.", ".$this->type.", new $control(), ".$this->autozoom.", $earth));
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
		if ($this->enableGeocoding) MapsGoogleMaps::addGMapDependencies($this->formOutput);		
	}	
	
}
