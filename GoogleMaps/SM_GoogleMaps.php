<?php
/**
 * A query printer for maps using the Google Maps API
 *
 * @file SM_GoogleMaps.php
 * @ingroup SemanticMaps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMaps extends SMMapPrinter {
	
	public function getName() {
		wfLoadExtensionMessages('SemanticMaps');
		return wfMsg('sm_googlemaps_printername');
	}
	
	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 *
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;

		$this->defaultZoom = $egMapsGoogleMapsZoom;
	}	
	
	/**
	 * @see SMMapPrinter::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;

		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies($this->output);
		}
		
		$egGoogleMapsOnThisPage++;	
		
		$this->elementNr = $egGoogleMapsOnThisPage;		
	}
	
	/**
	 * @see SMMapPrinter::getQueryResult()
	 *
	 */
	protected function addSpecificMapHTML() {
		global $wgJsMimeType;
				
		$enableEarth = MapsGoogleMaps::getEarthValue($this->earth);
		$this->earth = MapsGoogleMaps::getJSEarthValue($enableEarth);
		
		// Get the Google Maps names for the control and map types
		$this->type = MapsGoogleMaps::getGMapType($this->type, $enableEarth);
		$control = MapsGoogleMaps::getGControlType($this->controls);

		$markerItems = array();
		
		foreach ($this->m_locations as $location) {
			// Create a string containing the marker JS 
			list($lat, $lon, $title, $label, $icon) = $location;
			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);
			$markerItems[] = "getGMarkerData($lat, $lon, '$title', '$label')";
		}
		
		$markersString = implode(',', $markerItems);		
		
		$this->output .= <<<END
<div id="$this->mapName" class="$this->class" style="$this->style" ></div>
<script type="$wgJsMimeType"> /*<![CDATA[*/
addLoadEvent(
	initializeGoogleMap('$this->mapName', $this->width, $this->height, $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, new $control(), $this->autozoom, $this->earth, [$markersString])
);
/*]]>*/ </script>

END;
	
	}
	

}

