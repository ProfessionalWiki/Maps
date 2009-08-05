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
		
	protected $markerData = array();
	
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
		foreach($this->coordinates as $coordinates) {
			$coordinates = str_replace('″', '"', $coordinates);
			$coordinates = str_replace('′', "'", $coordinates);
			$this->markerData[] = MapsUtils::getLatLon($coordinates);
		}
	}
	
	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 *
	 */
	private function setCentre() {
		if (empty($this->centre)) {
			if (count($this->markerData) == 1) {
				$this->centre_lat = $this->markerData[0]['lat'];
				$this->centre_lon = $this->markerData[0]['lon'];
			}
			elseif (count($this->markerData) > 1) {
				// TODO
				die("// TODO: calculate centre and zoom (with SGM code?)"); 
			}
			else {
				global $egMapsMapLat, $egMapsMapLon;
				$this->centre_lat = $egMapsMapLat;
				$this->centre_lon = $egMapsMapLon;
			}
		}
		else {
			$centre = MapsUtils::getLatLon($this->centre);
			$this->centre_lat = $centre['lat'];
			$this->centre_lon = $centre['lon'];
		}		
	}	
	
}
