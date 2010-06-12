<?php

/**
 * This groupe contains all Google Maps v3 related files of the Maps extension.
 * 
 * @defgroup MapsGoogleMaps3 Google Maps v3
 * @ingroup Maps
 */

/**
 * This file holds the general information for the Google Maps v3 service.
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
 * Class for Google Maps v3 initialization.
 * 
 * @ingroup MapsGoogleMaps3
 * 
 * @author Jeroen De Dauw
 */
class MapsGoogleMaps3 extends MapsMappingService {
	
	function __construct() {
		parent::__construct(
			'googlemaps3',
			array( 'google3', 'googlemap3', 'gmap3', 'gmaps3' )
		);
	}
	
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