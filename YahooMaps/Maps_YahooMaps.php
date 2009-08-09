<?php

/**
 * Class for handling the Maps parser functions with Yahoo! Maps
 *
 * @file Maps_YahooMaps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsYahooMaps extends MapsBaseMap {

	const SERVICE_NAME = 'yahoomaps';
	
	public $serviceName = self::SERVICE_NAME;		
	
	/**
	 * @see MapsBaseMap::setFormInputSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom, $egMapsYahooMapsPrefix;
		
		$this->defaultParams = MapsYahooMapsUtils::getDefaultParams();
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
		$this->defaultZoom = $egMapsYahooMapsZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egYahooMapsOnThisPage;
		
		MapsYahooMapsUtils::addYMapDependencies($this->output);	
		$egYahooMapsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;
	}	
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */		
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$this->type = MapsYahooMapsUtils::getYMapType($this->type, true);
		
		$this->controls = MapsYahooMapsUtils::createControlsString($this->controls);
		
		MapsUtils::makePxValue($this->width);
		MapsUtils::makePxValue($this->height);

		$this->autozoom = MapsYahooMapsUtils::getAutozoomJSValue($this->autozoom);
		
		$markerItems = array();		
		
		// TODO: Refactor up
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];	
			$title = array_key_exists('title', $markerData) ? $markerData['title'] : $this->title;
			$label = array_key_exists('label', $markerData) ? $markerData['label'] : $this->label;
			$markerItems[] = "getYMarkerData($lat, $lon, '$title', '$label', '')";
		}		
		
		$markersString = implode(',', $markerItems);

		$this->types = explode(",", $this->types);
		
		$typesString = MapsYahooMapsUtils::createTypesString($this->types);		
		
		$this->output .= <<<END
		<div id="$this->mapName" style="width: $this->width; height: $this->height;"></div>  
		
		<script type="$wgJsMimeType">/*<![CDATA[*/
		addLoadEvent(
			initializeYahooMap('$this->mapName', $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$typesString], [$this->controls], $this->autozoom, [$markersString])
		);
			/*]]>*/</script>
END;
	}

}
