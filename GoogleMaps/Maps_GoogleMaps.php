<?php

/**
 * Class for handling the Maps parser functions with Google Maps
 *
 * @file Maps_GoogleMaps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGoogleMaps extends MapsBaseMap {
	
	const SERVICE_NAME = 'googlemaps';
	
	public $serviceName = self::SERVICE_NAME;

	/**
	 * @see MapsBaseMap::setFormInputSettings()
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
		global $wgJsMimeType;
		
		$enableEarth = MapsGoogleMapsUtils::getEarthValue($this->earth);
		
		$this->type = MapsGoogleMapsUtils::getGMapType($this->type, true);
		
		$this->controls = MapsGoogleMapsUtils::createControlsString($this->controls);	
		
		$this->autozoom = MapsGoogleMapsUtils::getAutozoomJSValue($this->autozoom);
		
		$markerItems = array();		
		
		// TODO: Refactor up
		// TODO: Escaping
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];
			$title = array_key_exists('title', $markerData) ? $markerData['title'] : $this->title;
			$label = array_key_exists('label', $markerData) ? $markerData['label'] : $this->label;
			$icon = array_key_exists('icon', $markerData) ? $markerData['icon'] : '';
			$markerItems[] = "getGMarkerData($lat, $lon, '$title', '$label', '$icon')";
		}		
		
		$markersString = implode(',', $markerItems);	
		
		$this->types = explode(",", $this->types);
		
		$typesString = MapsGoogleMapsUtils::createTypesString($this->types, $enableEarth);
		
		$this->output .=<<<END

<div id="$this->mapName" class="$this->class" style="$this->style" ></div>
<script type="$wgJsMimeType"> /*<![CDATA[*/
addLoadEvent(
	initializeGoogleMap('$this->mapName', $this->width, $this->height, $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$typesString], [$this->controls], $this->autozoom, [$markersString])
);
/*]]>*/ </script>

END;

	}
	
}

