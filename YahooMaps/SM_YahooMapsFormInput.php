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
	
	public $serviceName = MapsYahooMaps::SERVICE_NAME;	
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom, $egMapsYahooMapsPrefix;
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
		$this->showAddresFunction = 'showYAddress';		

		$this->earthZoom = 17;	

		$this->defaultParams = MapsYahooMapsUtils::getDefaultParams();
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
			MapsYahooMapsUtils::addYMapDependencies($this->output);
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
		
		$type = MapsYahooMapsUtils::getYMapType($this->type, true);
		
		$this->autozoom = MapsYahooMapsUtils::getAutozoomJSValue($this->autozoom);
		
		$controlItems = MapsYahooMapsUtils::createControlsString($this->controls);		
		
		MapsUtils::makePxValue($this->width);
		MapsUtils::makePxValue($this->height);	
		
		$this->types = explode(",", $this->types);
		
		$typesString = MapsYahooMapsUtils::createTypesString($this->types);			
		
		$this->output .="
		<div id='".$this->mapName."' style='width: $this->width; height: $this->height;'></div>  
		
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputYahooMap('$this->mapName', '$this->coordsFieldName', $this->centre_lat, $this->centre_lon, $this->zoom, $type, [$typesString], [$controlItems], $this->autozoom, $this->marker_lat, $this->centre_lon));
		/*]]>*/</script>";		
	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {
		global $egYahooMapsKey;
		$this->enableGeocoding = strlen(trim($egYahooMapsKey)) > 0;
		if ($this->enableGeocoding) MapsYahooMapsUtils::addYMapDependencies($this->output);			
	}

	
}
