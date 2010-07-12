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
	
	/**
	 * @see iMappingService::getDefaultZoom
	 * 
	 * @since 0.6.5
	 */	
	public function getDefaultZoom() {
		global $egMapsOSMZoom;
		return $egMapsOSMZoom;
	}

	/**
	 * @see MapsMappingService::getMapId
	 * 
	 * @since 0.6.5
	 */
	public function getMapId( $increment = true ) {
		global $egMapsOSMPrefix, $egOSMOnThisPage;
		
		if ( $increment ) {
			$egOSMOnThisPage++;
		}
		
		return $egMapsOSMPrefix . '_' . $egOSMOnThisPage;
	}		
	
}