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
	
	// TODO: move this abstract function to a new MApsBaseMapUtils file?
	//protected abstract static function getDefaultParams();
	
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
		
		$this->setCoordinates();		
		
		$this->setZoom();
		
		$this->setCentre();
		
		$this->addSpecificMapHTML();

		return $this->output;		
	}
	
	/**
	 * Sets the zoom level to the provided value. When no zoom is provided, set
	 * it to the default when there is only one location, or the best fitting soom when
	 * there are multiple locations.
	 *
	 */
	private function setZoom() {
		if (strlen($this->zoom) < 1) {
			if (count($this->markerData) > 1) {
		        $this->zoom = 'null';
		    }
		    else {
		        $this->zoom = $this->defaultZoom;
		    }
		}				
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
				// If centre is not set and there is exactelly one marker, use it's coordinates.
				$this->centre_lat = $this->markerData[0]['lat'];
				$this->centre_lon = $this->markerData[0]['lon'];
			}
			elseif (count($this->markerData) > 1) {
				// If centre is not set and there are multiple markers, set the values to null,
				// to be auto determined by the JS of the mapping API.
				$this->centre_lat = 'null';
				$this->centre_lon = 'null';
			}
			else {
				// If centre is not set and there are no markers, use the default latitude and longitutde.
				global $egMapsMapLat, $egMapsMapLon;
				$this->centre_lat = $egMapsMapLat;
				$this->centre_lon = $egMapsMapLon;
			}
		}
		else {
			// If a centre value is set, use it.
			$centre = MapsUtils::getLatLon($this->centre);
			$this->centre_lat = $centre['lat'];
			$this->centre_lon = $centre['lon'];
		}		
	}	
	
}
