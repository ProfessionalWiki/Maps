<?php

/**
 * Class for handling the display_point(s) parser functions with Google Maps
 *
 * @file Maps_GoogleMapsDispPoint.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGoogleMapsDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsGoogleMapsUtils::SERVICE_NAME;

	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->defaultParams = MapsGoogleMapsUtils::getDefaultParams();
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;
		$this->defaultZoom = $egMapsGoogleMapsZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;
		
		MapsGoogleMapsUtils::addGMapDependencies($this->output);
		$egGoogleMapsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType, $wgOut;
		
		$enableEarth = MapsGoogleMapsUtils::getEarthValue($this->earth);
		
		$this->type = MapsGoogleMapsUtils::getGMapType($this->type, true);
		
		$this->controls = MapsGoogleMapsUtils::createControlsString($this->controls);	
		
		$onloadFunctions = MapsGoogleMapsUtils::addOverlayOutput($this->output, $this->mapName, $this->overlays, $this->controls);
		
		$this->autozoom = MapsGoogleMapsUtils::getAutozoomJSValue($this->autozoom);
		
		$markerItems = array();		
		
		// TODO: Refactor up
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];		
			
			$title = array_key_exists('title', $markerData) ? $markerData['title'] : $this->title;
			$label = array_key_exists('label', $markerData) ? $markerData['label'] : $this->label;

			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);	

			$icon = array_key_exists('icon', $markerData) ? $markerData['icon'] : '';
			$markerItems[] = "getGMarkerData($lat, $lon, '$title', '$label', '$icon')";
		}		
		
		$markersString = implode(',', $markerItems);	
		
		$this->types = explode(",", $this->types);
		
		$typesString = MapsGoogleMapsUtils::createTypesString($this->types, $enableEarth);
		
		$this->output .=<<<END
			
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
	
}

