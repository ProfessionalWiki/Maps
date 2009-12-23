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

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['yahoomaps'] = array(
									'pf' => array(
										'display_point' => array('class' => 'MapsYahooMapsDispPoint', 'file' => 'YahooMaps/Maps_YahooMapsDispPoint.php', 'local' => true),
										'display_map' => array('class' => 'MapsYahooMapsDispMap', 'file' => 'YahooMaps/Maps_YahooMapsDispMap.php', 'local' => true),
										),
									'classes' => array(
											array('class' => 'MapsYahooMaps', 'file' => 'YahooMaps/Maps_YahooMapsUtils.php', 'local' => true)											
											),
									'aliases' => array('yahoo', 'yahoomap', 'ymap', 'ymaps'),
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
		self::initializeParams();
	}
	
	private static function initializeParams() {
		global $egMapsServices, $egMapsYahooAutozoom, $egMapsYahooMapsType, $egMapsYahooMapsTypes, $egMapsYahooMapsZoom, $egMapsYMapControls;
		
		$allowedTypes = MapsYahooMaps::getTypeNames();
		
		$egMapsServices[self::SERVICE_NAME]['parameters'] = array(
									'zoom' => array (
										'default' => $egMapsYahooMapsZoom
										),
									'controls' => array(
										'type' => array('string', 'list'),
										'criteria' => array(
											'in_array' => self::getControlNames()
										),
										'default' => $egMapsYMapControls,
										'output-type' => array('list', ',', '\'')		
										),
									'type' => array (
										'aliases' => array('map-type', 'map type'),
										'criteria' => array(
											'in_array' => $allowedTypes			
											),
										'default' => $egMapsYahooMapsType										
										),
									'types' => array (
										'type' => array('string', 'list'),
										'aliases' => array('map-types', 'map types'),
										'criteria' => array(
											'in_array' => $allowedTypes
											),
										'default' =>  $egMapsYahooMapsTypes										
										),			
									'autozoom' => array(
										'aliases' => array('auto zoom', 'mouse zoom', 'mousezoom'),
										'criteria' => array(
											'in_array' => array('on', 'off', 'yes', 'no')	
											),
										'default' => $egMapsYahooAutozoom ? 'on' : 'off' 												
										),		
									);
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
		return array_keys(self::$mapTypes);
	}

	/**
	 * Returns the names of all supported controls. 
	 * This data is a copy of the one used to actually translate the names
	 * into the controls, since this resides client side, in YahooMapFunctions.js. 
	 * 
	 * @return array
	 */
	public static function getControlNames() {
		return array('scale', 'type', 'pan', 'zoom', 'zoom-short', 'auto-zoom');
	}	
		
	/**
	 * Returns the Yahoo Map type (defined in MapsYahooMaps::$mapTypes) 
	 * for the provided a general map type. When no match is found, the first 
	 * Google Map type will be returned as default.
	 *
	 * @param string $type
	 * @param boolean $restoreAsDefault
	 * 
	 * @return string or false
	 */
	public static function getYMapType($type, $restoreAsDefault = false) {
		global $egMapsYahooMapsType;
		
		$typeIsValid = array_key_exists($type, self::$mapTypes);
		
		if (!$typeIsValid && $restoreAsDefault) $type = $egMapsYahooMapsType;
		
		return $typeIsValid || $restoreAsDefault ? self::$mapTypes[ $type ] : false;	
	}

	/**
	 * Add references to the Yahoo! Maps API and required JS file to the provided output 
	 *
	 * @param string $output
	 */
	public static function addYMapDependencies(&$output) {
		global $wgJsMimeType;
		global $egYahooMapsKey, $egMapsScriptPath, $egYahooMapsOnThisPage, $egMapsStyleVersion;
		
		if (empty($egYahooMapsOnThisPage)) {
			$egYahooMapsOnThisPage = 0;
			$output .= "<script type='$wgJsMimeType' src='http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=$egYahooMapsKey'></script>
			<script type='$wgJsMimeType' src='$egMapsScriptPath/YahooMaps/YahooMapFunctions.js?$egMapsStyleVersion'></script>";
		}
	}

	/**
	 * Retuns a boolean as string, true if $autozoom is on or yes.
	 *
	 * @param string $autozoom
	 * @return string
	 */
	public static function getAutozoomJSValue($autozoom) {
		return MapsMapper::getJSBoolValue(in_array($autozoom, array('on', 'yes')));
	}	

	/**
	 * Returns a JS items string with the provided types. The earth type will
	 * be added to it when it's not present and $enableEarth is true. If there are
	 * no types, the default will be used.
	 *
	 * @param array $types
	 * @param boolean $enableEarth
	 * @return string
	 */
	public static function createTypesString(array &$types) {	
		global $egMapsYahooMapsTypes, $egMapsYahooMapTypesValid;
		
		$types = MapsMapper::getValidTypes($types, $egMapsYahooMapsTypes, $egMapsYahooMapTypesValid, array(__CLASS__, 'getYMapType'));
			
		return MapsMapper::createJSItemsString($types, null, false, false);
	}		
	
}									