<?php

/**
 * This groupe contains all Yahoo! Maps related files of the Maps extension.
 * 
 * @defgroup MapsYahooMaps Yahoo! Maps
 * @ingroup Maps
 */

/**
 * This file holds the general information for the Yahoo! Maps service
 *
 * @file Maps_YahooMaps.php
 * @ingroup MapsYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsYahooMaps'] = dirname( __FILE__ ) . '/Maps_YahooMaps.php';

$wgHooks['MappingServiceLoad'][] = 'MapsYahooMaps::initialize';

$wgAutoloadClasses['MapsYahooMapsDispMap'] = dirname( __FILE__ ) . '/Maps_YahooMapsDispMap.php';
$wgAutoloadClasses['MapsYahooMapsDispPoint'] = dirname( __FILE__ ) . '/Maps_YahooMapsDispPoint.php';

$egMapsServices[MapsYahooMaps::SERVICE_NAME] = array(
	'aliases' => array( 'yahoo', 'yahoomap', 'ymap', 'ymaps' ),
	'features' => array(
		'display_point' => 'MapsYahooMapsDispPoint',
		'display_map' => 'MapsYahooMapsDispMap',
	)
);

/**
 * Class for Yahoo! Maps initialization.
 * 
 * @ingroup MapsYahooMaps
 * 
 * @author Jeroen De Dauw
 */
class MapsYahooMaps {
	
	const SERVICE_NAME = 'yahoomaps';
	
	public static function initialize() {
		global $wgAutoloadClasses, $egMapsServices;

		self::initializeParams();
		
		Validator::addOutputFormat( 'ymaptype', array( __CLASS__, 'setYMapType' ) );
		Validator::addOutputFormat( 'ymaptypes', array( __CLASS__, 'setYMapTypes' ) );
		
		return true;
	}
	
	private static function initializeParams() {
		global $egMapsServices, $egMapsYahooAutozoom, $egMapsYahooMapsType, $egMapsYahooMapsTypes, $egMapsYahooMapsZoom, $egMapsYMapControls;
		
		$allowedTypes = MapsYahooMaps::getTypeNames();
		
		$egMapsServices[self::SERVICE_NAME]['parameters'] = array(
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
				
		$egMapsServices[self::SERVICE_NAME]['parameters']['zoom']['criteria']['in_range'] = array( 1, 13 );
	}
	
	// http://developer.yahoo.com/maps/ajax
	private static $mapTypes = array(
		'normal' => 'YAHOO_MAP_REG',
		'satellite' => 'YAHOO_MAP_SAT',
		'hybrid' => 'YAHOO_MAP_HYB',
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

	/**
	 * Loads the Yahoo! Maps API and required JS files.
	 *
	 * @param mixed $parserOrOut
	 */
	public static function addYMapDependencies( &$parserOrOut ) {
		global $wgJsMimeType;
		global $egYahooMapsKey, $egMapsScriptPath, $egYahooMapsOnThisPage, $egMapsStyleVersion, $egMapsJsExt;
		
		if ( empty( $egYahooMapsOnThisPage ) ) {
			$egYahooMapsOnThisPage = 0;

			if ( $parserOrOut instanceof Parser ) {
				$parser = $parserOrOut;
				
				$parser->getOutput()->addHeadItem( 
					Html::linkedScript( "http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=$egYahooMapsKey" ) .		
					Html::linkedScript( "$egMapsScriptPath/Services/YahooMaps/YahooMapFunctions{$egMapsJsExt}?$egMapsStyleVersion" )						
				);				
			}
			else if ( $parserOrOut instanceof OutputPage ) {
				$out = $parserOrOut;
				MapsMapper::addScriptFile( $out, "http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=$egYahooMapsKey" );
				$out->addScriptFile( "$egMapsScriptPath/Services/YahooMaps/YahooMapFunctions{$egMapsJsExt}?$egMapsStyleVersion" );
			}			
		}
	}
	
}									