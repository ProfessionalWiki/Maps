<?php
/**
 * A query printer for maps using the Google Maps API
 *
 * @file SM_GoogleMaps.php
 * @ingroup SMGoogleMaps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMapsQP extends SMMapPrinter {
	
	public $serviceName = MapsGoogleMaps::SERVICE_NAME;
	
	protected $spesificParameters;
	
	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 *
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;

		$this->defaultZoom = $egMapsGoogleMapsZoom;
		
		$this->spesificParameters = array(
			'zoom' => array(
				'default' => '', 	
			),
			'overlays' => array(
				'aliases' => array(),
				'criteria' => array(),
				'default' => ''												
			),				
		);			
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
		
		// Get the Google Maps names for the control and map types.
		$this->type = MapsGoogleMaps::getGMapType($this->type, true);
		
		$this->controls = MapsMapper::createJSItemsString(explode(',', $this->controls));

		$onloadFunctions = MapsGoogleMaps::addOverlayOutput($this->output, $this->mapName, $this->overlays, $this->controls);
		
		$this->autozoom = MapsGoogleMaps::getAutozoomJSValue($this->autozoom);
		
		$markerItems = array();
		
		foreach ($this->m_locations as $location) {
			list($lat, $lon, $title, $label, $icon) = $location;
			
			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);
			
			$markerItems[] = "getGMarkerData($lat, $lon, '$title', '$label', '$icon')";
		}
		
		// Create a string containing the marker JS 
		$markersString = implode(',', $markerItems);		
		
		$this->types = explode(",", $this->types);
		
		$typesString = MapsGoogleMaps::createTypesString($this->types);		
		
		$this->output .= <<<END
<div id="$this->mapName" class="$this->class" style="$this->style" ></div>
<script type="$wgJsMimeType"> /*<![CDATA[*/
addOnloadHook(
	initializeGoogleMap('$this->mapName', 
		{
		width: $this->width,
		height: $this->height,
		lat: $this->centre_lat,
		lon: $this->centre_lon,
		zoom: $this->zoom,
		type: $this->type,
		types: [$typesString],
		controls: [$this->controls],
		scrollWheelZoom: $this->autozoom
		},
		[$markersString]	
	)
);
/*]]>*/ </script>

END;
	
		$this->output .= $onloadFunctions;	
	}
	
	public function getParameters() {
		return array_merge(parent::getParameters(), 
			array()
			); 
	}
	
}

