<?php

/**
 * File holding the MapsGoogleMaps3DispPoint class.
 *
 * @file Maps_GoogleMaps3DispPoint.php
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with Google Maps v3.
 *
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMaps3DispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsGoogleMaps3::SERVICE_NAME;

	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsGoogleMaps3Zoom, $egMapsGoogleMaps3Prefix;
		
		$this->elementNamePrefix = $egMapsGoogleMaps3Prefix;
		$this->defaultZoom = $egMapsGoogleMaps3Zoom;
		
		$this->markerStringFormat = ''; // TODO
		
		$this->spesificParameters = array(
		);		
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egGoogleMaps3OnThisPage;
		
		MapsGoogleMaps3::addGMap3Dependencies($this->output);
		$egGoogleMaps3OnThisPage++;
		
		$this->elementNr = $egGoogleMaps3OnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$this->output .=<<<END

END;

	}
	
}

