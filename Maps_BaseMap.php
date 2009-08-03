<?php

/**
 * MapsBaseMap is an abstract class inherited by the map services classes
 *
 * @file Maps_BaseMap.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

abstract class MapsBaseMap extends MapsMapFeature {
		
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param unknown_type $parser
	 * @param unknown_type $map
	 * @return unknown
	 */
	public final function displayMap(&$parser, $map) {
		$this->setMapSettings();
		$this->doMapServiceLoad();

		$this->setMapName();
		
		$this->manageMapProperties($map, 'MapsBaseMap');
		
		$this->autozoom = ($this->autozoom == 'no' || $this->autozoom == 'off') ? 'false' : 'true';	
		
		$this->setZoom();
		
		$this->setCoordinates();
		$this->setCentre();
		
		$this->addSpecificMapHTML();

		return $this->output;		
	}
	

	
	/**
	 * Sets the zoom level to the provided value, or when not set, to the default.
	 *
	 */
	private function setZoom() {
		if (strlen($this->zoom) < 1) $this->zoom = $this->defaultZoom;	
	}	
	
	/**
	 * Sets the $marler_lon and $marler_lat fields.
	 *
	 */
	private function setCoordinates() {
		$this->coordinates = str_replace('″', '"', $this->coordinates);
		$this->coordinates = str_replace('′', "'", $this->coordinates);		
		list($this->marker_lat, $this->marker_lon) = MapsUtils::getLatLon($this->coordinates);
	}
	
	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 *
	 */
	private function setCentre() {
		if (empty($this->centre)) {
			$this->centre_lat = $this->marker_lat;
			$this->centre_lon = $this->marker_lon;
		}
		else {
			list($this->centre_lat, $this->centre_lon) = MapsUtils::getLatLon($this->centre);
		}		
	}	
	
}
