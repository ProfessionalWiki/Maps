<?php

/**
 * Class for handling the display_map parser function with OSM
 *
 * @file Maps_OSMDispMap.php
 * @ingroup OSM
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsOSMDispMap extends MapsBaseMap {
	
	protected function getDefaultZoom() {
		global $egMapsOSMZoom;
		return $egMapsOSMZoom;
	}	
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML() {	
		// TODO
	}
	
}