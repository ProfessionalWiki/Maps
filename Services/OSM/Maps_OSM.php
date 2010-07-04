<?php

/**
 * File holding the MapsOSM class.
 *
 * @file Maps_OSM.php
 * @ingroup OSM
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * TODO
 * 
 * @ingroup OSM
 * 
 * @author Jeroen De Dauw
 */
class MapsOpenLayers extends MapsMappingService {
	
	function __construct() {
		parent::__construct(
			'osm',
			array( 'openstreetmap' )
		);
	}
	
}