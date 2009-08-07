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
		global $egMapsGoogleMapsTypes;
		
		$enableEarth = MapsGoogleMapsUtils::getEarthValue($this->earth);
		$this->earth = MapsMapper::getJSBoolValue($enableEarth);
		
		$this->type = MapsGoogleMapsUtils::getGMapType($this->type, $enableEarth);
		$control = MapsGoogleMapsUtils::getGControlType($this->controls);	
		
		$this->autozoom = MapsGoogleMapsUtils::getAutozoomJSValue($this->autozoom);
		
		$markerItems = array();		
		
		// TODO: Refactor up
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];
			$markerItems[] = "getGMarkerData($lat, $lon, '$this->title', '$this->label', '')";
		}		
		
		$markersString = implode(',', $markerItems);	
		
		$this->types = explode(",", $this->types);
		
		if (count($this->types) < 1) $this->types = $egMapsGoogleMapsTypes;		
		
		for($i = 0 ; $i < count($this->types); $i++) {
			$this->types[$i] = MapsGoogleMapsUtils::getGMapType($this->types[$i], $enableEarth);
		}
		
		// This is to ensure backwards compatibility with 0.1 and 0.2.
		if ($enableEarth && ! in_array('G_SATELLITE_3D_MAP', $this->types)) $this->types[] = 'G_SATELLITE_3D_MAP';
		
		$typesString = MapsMapper::createJSItemsString($this->types, null, false, false);
		
		$this->output .=<<<END

<div id="$this->mapName" class="$this->class" style="$this->style" ></div>
<script type="$wgJsMimeType"> /*<![CDATA[*/
addLoadEvent(
	initializeGoogleMap('$this->mapName', $this->width, $this->height, $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$typesString], new $control(), $this->autozoom, $this->earth, [$markersString])
);
/*]]>*/ </script>

END;

	}
	
}

