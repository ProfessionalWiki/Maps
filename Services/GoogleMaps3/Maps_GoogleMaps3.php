<?php

/**
 * File holding the MapsGoogleMaps3 class.
 *
 * @file Maps_GoogleMaps3.php
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class holding information and functionallity specific to Google Maps v3.
 * This infomation and features can be used by any mapping feature. 
 * 
 * @since 0.1
 * 
 * @ingroup MapsGoogleMaps3
 * 
 * @author Jeroen De Dauw
 */
class MapsGoogleMaps3 extends MapsMappingService {
	
	/**
	 * Constructor
	 * 
	 * @since 0.6.3
	 */	
	function __construct() {
		parent::__construct(
			'googlemaps3',
			array( 'google3', 'googlemap3', 'gmap3', 'gmaps3' )
		);
	}
	
	/**
	 * @see MapsMappingService::initParameterInfo
	 * 
	 * @since 0.5
	 */	
	protected function initParameterInfo( array &$parameters ) {
		global $egMapsServices, $egMapsGMaps3Type, $egMapsGMaps3Types;
		
		Validator::addOutputFormat( 'gmap3type', array( __CLASS__, 'setGMapType' ) );
		Validator::addOutputFormat( 'gmap3types', array( __CLASS__, 'setGMapTypes' ) );		
		
		$allowedTypes = self::getTypeNames();
		
		$parameters = array(
			'type' => array(
				'aliases' => array( 'map-type', 'map type' ),
				'criteria' => array(
					'in_array' => $allowedTypes
				),
				'default' => $egMapsGMaps3Type, // FIXME: default value should not be used when not present in types parameter.
				'output-type' => 'gmap3type'
			),
				/*
			'types' => array(
				'type' => array('string', 'list'),
				'aliases' => array('map-types', 'map types'),
				'criteria' => array(
					'in_array' => $allowedTypes
				),
				'default' => $egMapsGMaps3Types,
				'output-types' => array('gmap3types', 'list')				
			),	
				*/
		);
	}
	
	/**
	 * @see iMappingService::getDefaultZoom
	 * 
	 * @since 0.6.5
	 */	
	public function getDefaultZoom() {
		global $egMapsGoogleMaps3Zoom;
		return $egMapsGoogleMaps3Zoom;
	}	
	
	/**
	 * @see MapsMappingService::getMapId
	 * 
	 * @since 0.6.5
	 */
	public function getMapId( $increment = true ) {
		global $egMapsGoogleMaps3Prefix, $egGoogleMaps3OnThisPage;
		
		if ( $increment ) {
			$egGoogleMaps3OnThisPage++;
		}
		
		return $egMapsGoogleMaps3Prefix . '_' . $egGoogleMaps3OnThisPage;
	}	
	
	/**
	 * @see MapsMappingService::createMarkersJs
	 * 
	 * @since 0.6.5
	 * 
	 * TODO: escaping!
	 */
	public function createMarkersJs( array $markers ) {
		$markerItems = array();
		
		foreach ( $markers as $marker ) {
			list( $lat, $lon, $title, $label, $icon ) = $marker;
			$markerItems[] = "getGMaps3MarkerData($lat, $lon, \"$title\", \"$label\", \"$icon\")";
		}
		
		// Create a string containing the marker JS.
		return '[' . implode( ',', $markerItems ) . ']';
	}	
	
	protected static $mapTypes = array(
		'normal' => 'ROADMAP',
		'roadmap' => 'ROADMAP',
		'satellite' => 'SATELLITE',
		'hybrid' => 'HYBRID',
		'terrain' => 'TERRAIN',
		'physical' => 'TERRAIN'
	);
	
	/**
	 * Returns the names of all supported map types.
	 * 
	 * @return array
	 */
	public static function getTypeNames() {
		return array_keys( self::$mapTypes );
	}
	
	/**
	 * Changes the map type name into the corresponding Google Maps API v3 identifier.
	 *
	 * @param string $type
	 * 
	 * @return string
	 */
	public static function setGMapType( &$type, $name, array $parameters ) {
		$type = 'google.maps.MapTypeId.' . self::$mapTypes[ $type ];
	}
	
	/**
	 * Changes the map type names into the corresponding Google Maps API v3 identifiers.
	 * 
	 * @param array $types
	 * 
	 * @return array
	 */
	public static function setGMapTypes( array &$types, $name, array $parameters ) {
		for ( $i = count( $types ) - 1; $i >= 0; $i-- ) {
			self::setGMapType( $types[$i], $name, $parameters );
		}
	}
	
	/**
	 * @see MapsMappingService::getDependencies
	 * 
	 * @return array
	 */
	protected function getDependencies() {
		global $wgLang;
		global $egMapsStyleVersion, $egMapsJsExt, $egMapsScriptPath;

		$languageCode = self::getMappedLanguageCode( $wgLang->getCode() );
		
		return array(
			Html::linkedScript( "http://maps.google.com/maps/api/js?sensor=false&language=$languageCode" ),
			Html::linkedScript( "$egMapsScriptPath/Services/GoogleMaps3/GoogleMap3Functions{$egMapsJsExt}?$egMapsStyleVersion" ),
		);			
	}
	
	/**
	 * Maps language codes to Google Maps API v3 compatible values.
	 * 
	 * @param string $code
	 * 
	 * @return string The mapped code
	 */
	protected static function getMappedLanguageCode( $code ) {
		$mappings = array(
	         'en_gb' => 'en-gb',// v3 supports en_gb - but wants us to call it en-gb
	         'he' => 'iw',      // iw is googlish for hebrew
	         'fj' => 'fil',     // google does not support Fijian - use Filipino as close(?) supported relative
		);
		
		if ( array_key_exists( $code, $mappings ) ) {
			$code = $mappings[$code];
		}
		
		return $code;
	}
	
}								