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
 * Class holding information and functionallity specific to OSM.
 * This infomation and features can be used by any mapping feature. 
 * 
 * @since 0.6.4
 * 
 * @ingroup OSM
 * 
 * @author Jeroen De Dauw
 */
class MapsOSM extends MapsMappingService {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.6.4
	 */
	function __construct() {
		parent::__construct(
			'osm',
			array( 'openstreetmap' )
		);
	}
	
}