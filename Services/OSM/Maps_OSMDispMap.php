<?php

/**
 * File holding the MapsOSMDispMap class.
 *
 * @file Maps_OSMDispMap.php
 * @ingroup OSM
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_map parser function with OSM.
 * 
 * @since 0.6.4
 * 
 * @ingroup OSM
 * 
 * @author Jeroen De Dauw
 */
class MapsOSMDispMap extends MapsBaseMap {
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 * 
	 * @since 0.6.4
	 */
	public function addSpecificMapHTML() {	
		$this->output .= Html::element(
			'iframe',
			array(
				'id' => $this->service->getMapId(),
				'style' => "width: $this->width; height: $this->height; clear: both;",
				'src' => "http://toolserver.org/~kolossos/openlayers/kml-on-ol.php?zoom={$this->zoom}&lat={$this->centreLat}&lon={$this->centreLon}&lang=en"
			),
			wfMsg( 'maps-loading-map' )
		);
	}
	
}