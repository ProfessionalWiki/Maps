<?php

/**
 * Class holding information and functionality specific to OSM.
 * This information and features can be used by any mapping feature.
 * 
 * @since 0.6.4
 * 
 * @file Maps_OSM.php
 * @ingroup OSM
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsOSM extends MapsMappingService {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.6.6
	 */
	function __construct( $serviceName ) {
		parent::__construct(
			$serviceName,
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
		static $mapsOnThisPage = 0;
		
		if ( $increment ) {
			$mapsOnThisPage++;
		}
		
		return 'map_osm_' . $mapsOnThisPage;
	}

	/**
	 * @see MapsMappingService::addParameterInfo
	 * 
	 * @since 0.7
	 */		
	public function addParameterInfo( array &$params ) {
		global $egMapsOSMThumbs, $egMapsOSMPhotos;

		$params['zoom']->setRange( 1, 18 );
		$params['zoom']->setDefault( self::getDefaultZoom() );

		$params['thumbs'] = array(
			'type' => 'boolean',
			'default' => $egMapsOSMThumbs,
			'message' => 'maps-osm-par-thumbs',
		);

		$params['photos'] = array(
			'type' => 'boolean',
			'default' => $egMapsOSMPhotos,
			'message' => 'maps-osm-par-photos',
		);
	}
	
}
