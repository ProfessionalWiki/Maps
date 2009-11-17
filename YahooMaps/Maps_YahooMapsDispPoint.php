<?php

/**
 * File holding the MapsYahooMapsDispPoint class.
 *
 * @file Maps_YahooMapsDispPoint.php
 * @ingroup MapsYahooMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with Yahoo! Maps.
 *
 * @author Jeroen De Dauw
 */
class MapsYahooMapsDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsYahooMapsUtils::SERVICE_NAME;		
	
	/**
	 * @see MapsBaseMap::setFormInputSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom, $egMapsYahooMapsPrefix;
		
		$this->defaultParams = MapsYahooMapsUtils::getDefaultParams();
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
		$this->defaultZoom = $egMapsYahooMapsZoom;
		
		$this->markerStringFormat = 'getYMarkerData(lat, lon, "title", "label", "icon")';
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

		$this->autozoom = MapsYahooMapsUtils::getAutozoomJSValue($this->autozoom);

		$this->types = explode(",", $this->types);
		
		$typesString = MapsYahooMapsUtils::createTypesString($this->types);		
		
		$this->output .= <<<END
		<div id="$this->mapName" style="width: {$this->width}px; height: {$this->height}px;"></div>  
		
		<script type="$wgJsMimeType">/*<![CDATA[*/
		addOnloadHook(
			initializeYahooMap('$this->mapName', $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$typesString], [$this->controls], $this->autozoom, [$this->markerString])
		);
			/*]]>*/</script>
END;
	}

}
