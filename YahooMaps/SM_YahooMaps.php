<?php
/**
 * A query printer for maps using the Yahoo Maps API
 *
 * @file SM_YahooMaps.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMYahooMaps extends SMMapPrinter {
	// TODO: create a class instead of a code horror :D

	public function getName() {
		wfLoadExtensionMessages('SemanticMaps');
		return wfMsg('sm_yahoomaps_printername');
	}
	
	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 *
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsYahooMapsZoom;
		
		$this->elementNamePrefix = 'map_yahoo';
		$this->defaultZoom = $egMapsYahooMapsZoom;		
	}
	
	/**
	 * @see SMMapPrinter::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egYahooMapsOnThisPage;
		
		MapsYahooMaps::addYMapDependencies($this->output);	
		$egYahooMapsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;		
	}
	
	/**
	 * @see SMMapPrinter::addSpecificMapHTML()
	 *
	 */
	protected function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$this->type = MapsYahooMaps::getYMapType($this->type);
		$this->controls = MapsYahooMaps::createControlsString($this->controls);
		
		MapsUtils::makePxValue($this->width);
		MapsUtils::makePxValue($this->height);		
		
		$markerItems = array();
		
		foreach ($this->m_locations as $location) {
			// Create a string containing the marker JS 
			list($lat, $lon, $title, $label, $icon) = $location;
			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);
			$markerItems[] = "getYMarkerData($lat, $lon, '$title', '$label', '')";
		}
		
		$markersString = implode(',', $markerItems);		
		
		$this->output .= "
		<div id='$this->mapName' style='width: $this->width; height: $this->height;'></div>  
		
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(
			initializeYahooMap('$this->mapName', $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$this->controls], $this->autozoom, [$markersString])
		);
			/*]]>*/</script>";		

	}	

}