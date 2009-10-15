<?php

/**
 * Abstract class MapsBaseMap provides the scafolding for classes handling display_map
 * calls for a spesific mapping service. It inherits from MapsMapFeature and therefore
 * forces inheriting classes to implement sereveral methods.
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
	 * @param array $params
	 * 
	 * @return html
	 */
	public final function displayMap(&$parser, array $params) {			
die('disp map');
		
		$this->setMapSettings();
		
		$coords = $this->manageMapProperties($params);
		
		$this->doMapServiceLoad();

		$this->setMapName();	
		
		$this->setZoom();
		
		$this->setCentre();
		
		$this->addSpecificMapHTML();			
		
		return $this->output;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see smw/extensions/Maps/MapsMapFeature#manageMapProperties($mapProperties, $className)
	 */
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
		if (strlen($this->zoom) < 1) $this->zoom = $this->defaultZoom;			
	}	
	
	private function setCentre() {
		if (strlen($this->centre) < 1) $this->centre = $this->defaultZoom;	
	}
	
}
