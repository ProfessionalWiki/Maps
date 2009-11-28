<?php

/**
 * File holding the MapsGoogleMapsDispPoint class.
 *
 * @file Maps_GoogleMapsDispPoint.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with Google Maps.
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMapsDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsGoogleMaps::SERVICE_NAME;

	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;
		$this->defaultZoom = $egMapsGoogleMapsZoom;
		
		$this->markerStringFormat = 'getGMarkerData(lat, lon, "title", "label", "icon")';
		
		$this->spesificParameters = array(
			'overlays' => array(
				'aliases' => array(),
				'criteria' => array(),
				'default' => ''												
				),	
			'zoom' => array(
				'default' => '', 	
			)							
		);		
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;
		
		MapsGoogleMaps::addGMapDependencies($this->output);
		$egGoogleMapsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$this->type = MapsGoogleMaps::getGMapType($this->type, true);
		
		$this->controls = MapsMapper::createJSItemsString(explode(',', $this->controls));	
		
		$onloadFunctions = MapsGoogleMaps::addOverlayOutput($this->output, $this->mapName, $this->overlays, $this->controls);
		
		$this->autozoom = MapsGoogleMaps::getAutozoomJSValue($this->autozoom);
		
		$this->types = explode(",", $this->types);
		
		$typesString = MapsGoogleMaps::createTypesString($this->types);
		
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
		[$this->markerString]
	)
);
/*]]>*/ </script>

END;

	$this->output .= $onloadFunctions;

	}
	
}

