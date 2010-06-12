<?php

/**
 * File holding the MapsYahooMaps class.
 *
 * @file Maps_YahooMaps.php
 * @ingroup MapsYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * TODO
 * 
 * @ingroup MapsYahooMaps
 * 
 * @author Jeroen De Dauw
 */
class MapsYahooMaps extends MapsMappingService {

	/**
	 * Mapping for Yahoo! Maps map types. 
	 * See http://developer.yahoo.com/maps/ajax
	 * 
	 * @var array
	 */
	protected static $mapTypes = array(
		'normal' => 'YAHOO_MAP_REG',
		'satellite' => 'YAHOO_MAP_SAT',
		'hybrid' => 'YAHOO_MAP_HYB',
	);	
	
	function __construct() {
		parent::__construct(
			'yahoomaps',
			array( 'yahoo', 'yahoomap', 'ymap', 'ymaps' )
		);
	}		
	
	protected function initParameterInfo( array &$parameters ) {
		global $egMapsServices, $egMapsYahooAutozoom, $egMapsYahooMapsType, $egMapsYahooMapsTypes, $egMapsYahooMapsZoom, $egMapsYMapControls;
		
		Validator::addOutputFormat( 'ymaptype', array( __CLASS__, 'setYMapType' ) );
		Validator::addOutputFormat( 'ymaptypes', array( __CLASS__, 'setYMapTypes' ) );		
		
		$allowedTypes = MapsYahooMaps::getTypeNames();
		
		$parameters = array(
			'controls' => array(
				'type' => array( 'string', 'list' ),
				'criteria' => array(
					'in_array' => self::getControlNames()
				),
				'default' => $egMapsYMapControls,
				'output-type' => array( 'list', ',', '\'' )
			),
			'type' => array (
				'aliases' => array( 'map-type', 'map type' ),
				'criteria' => array(
					'in_array' => $allowedTypes
				),
				'default' => $egMapsYahooMapsType, // FIXME: default value should not be used when not present in types parameter.
				'output-type' => 'ymaptype',
				'dependencies' => array( 'types' )
			),
			'types' => array (
				'type' => array( 'string', 'list' ),
				'aliases' => array( 'map-types', 'map types' ),
				'criteria' => array(
					'in_array' => $allowedTypes
				),
				'default' =>  $egMapsYahooMapsTypes,
				'output-types' => array( 'ymaptypes', 'list' )
			),
			'autozoom' => array(
				'type' => 'boolean',
				'aliases' => array( 'auto zoom', 'mouse zoom', 'mousezoom' ),
				'default' => $egMapsYahooAutozoom,
				'output-type' => 'boolstr'
			),
		);
		
		$parameters['zoom']['criteria']['in_range'] = array( 1, 13 );
	}
	
	/**
	 * @see MapsMappingService::getDependencies
	 * 
	 * @return array
	 */
	protected function getDependencies() {
		global $wgJsMimeType;
		global $egYahooMapsKey, $egMapsScriptPath, $egMapsStyleVersion, $egMapsJsExt;
		
		return array(
			Html::linkedScript( "http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=$egYahooMapsKey" ),
			Html::linkedScript( "$egMapsScriptPath/Services/YahooMaps/YahooMapFunctions{$egMapsJsExt}?$egMapsStyleVersion" ),
		);		
	}	
	
	/**
	 * Returns the names of all supported map types.
	 * 
	 * @return array
	 */
	public static function getTypeNames() {
		return array_keys( self::$mapTypes );
	}

	/**
	 * Returns the names of all supported controls. 
	 * This data is a copy of the one used to actually translate the names
	 * into the controls, since this resides client side, in YahooMapFunctions.js. 
	 * 
	 * @return array
	 */
	public static function getControlNames() {
		return array( 'scale', 'type', 'pan', 'zoom', 'zoom-short', 'auto-zoom' );
	}
	
	/**
	 * Changes the map type name into the corresponding Yahoo! Maps API identifier.
	 *
	 * @param string $type
	 * 
	 * @return string
	 */
	public static function setYMapType( &$type, $name, array $parameters ) {
		$type = self::$mapTypes[ $type ];
	}
	
	/**
	 * Changes the map type names into the corresponding Yahoo! Maps API identifiers.
	 * 
	 * @param array $types
	 * 
	 * @return array
	 */
	public static function setYMapTypes( array &$types, $name, array $parameters ) {
		for ( $i = count( $types ) - 1; $i >= 0; $i-- ) {
			$types[$i] = self::$mapTypes[ $types[$i] ];
		}
	}

}									