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
	
	// TODO: move this abstract function to a new MapsBaseMapUtils file?
	//protected abstract static function getDefaultParams();
	
	protected $markerData = array();
	
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param unknown_type $parser
	 * @param array $map
	 * @return html
	 */
	public final function displayMap(&$parser, array $params) {
		global $wgLang;
		
		$this->setMapSettings();
		
		$coords = $this->manageMapProperties($params);
		
		$this->doMapServiceLoad();

		$this->setMapName();
		
		$this->setCoordinates();		
		
		$this->setZoom();
		
		$this->setCentre();
		
		$this->doParsing($parser);
		
		$this->doEscaping();
		
		$this->addSpecificMapHTML();			
		
		return $this->output;
	}
	
	protected function manageMapProperties($params) {
		parent::manageMapProperties($params, __CLASS__);
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
	 * Fills the $markerData array with the locations and their meta data.
	 *
	 */
	private function setCoordinates() {
		$this->coordinates = explode(';', $this->coordinates);		
		
		foreach($this->coordinates as $coordinates) {
			$args = explode('~', $coordinates);
			
			$args[0] = str_replace('″', '"', $args[0]);
			$args[0] = str_replace('′', "'", $args[0]);			
			
			$markerData = MapsUtils::getLatLon($args[0]);
			
			if (count($args) > 1) {
				$markerData['title'] = $args[1];
				
				if (count($args) > 2) {
					$markerData['label'] = $args[2];
					
					if (count($args) > 3) {
						$markerData['icon'] = $args[3];
					}					
				}
			}

			$this->markerData[] = $markerData;
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
	
	/**
	 * Parse the wiki text in the title and label values.
	 * 
	 * @param $parser
	 */
	private function DoParsing(&$parser) {
		$this->title = $parser->recursiveTagParse( $this->title );
		$this->label = $parser->recursiveTagParse( $this->label );
	}
	
	/**
	 * Escape the title and label text
	 *
	 */
	private function doEscaping() {
		$this->title = str_replace("'", "\'", $this->title);
		$this->label = str_replace("'", "\'", $this->label);		
	}
	
}
