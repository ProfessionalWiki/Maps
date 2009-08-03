<?php

/**
* Form input hook that adds an Yahoo! Maps map format to Semantic Forms
 *
 * @file SM_YahooMapsFormInput.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMYahooMapsFormInput extends SMFormInput {
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom;
		
		$this->elementNamePrefix = 'map_yahoo';
		$this->showAddresFunction = 'showYAddress';		

		$this->earthZoom = 17;
		$this->defaultZoom = $egMapsYahooMapsZoom;			
	}	
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egYahooMapsOnThisPage;
		
		if (empty($egYahooMapsOnThisPage)) {
			$egYahooMapsOnThisPage = 0;
			MapsYahooMaps::addYMapDependencies($this->output);
		}
		$egYahooMapsOnThisPage++;			
		
		$this->elementNr = $egYahooMapsOnThisPage;
	}	
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML()
	 *
	 */
	protected function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$type = MapsYahooMaps::getYMapType($this->type);
		
		$controlItems = MapsYahooMaps::createControlsString($this->controls);		
		
		$width = $this->width . 'px';
		$height = $this->height . 'px';		
		
		$this->output .="
		<div id='".$this->mapName."' style='width: $width; height: $height;'></div>  
		
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputYahooMap('".$this->mapName."', '".$this->coordsFieldName."', ".$this->centre_lat.", ".$this->centre_lon.", ".$this->zoom.", ".$this->marker_lat.", ".$this->marker_lon.", $type, [$controlItems], ".$this->autozoom."));
		/*]]>*/</script>";		
	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {
		global $egYahooMapsKey;
		$this->enableGeocoding = strlen(trim($egYahooMapsKey)) > 0;
		if ($this->enableGeocoding) MapsYahooMaps::addYMapDependencies($this->output);			
	}

	
}
