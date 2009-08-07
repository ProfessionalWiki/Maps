<?php

/**
 * A class that holds static helper functions for common functionality that is not map-spesific.
 *
 * @file Maps_Mapper.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsMapper {
	
	/**
	 * Array holding the allowed main parameters and their alias. 
	 * The array keys hold the main name, and the values are arrays holding the aliases.
	 *
	 * @var array
	 */
	private static $mainParams = array
			(
			'service' => array(),
			'coordinates' => array('coords', 'location'),
			'zoom' => array(),
			'centre' => array('center'),
			'width' => array(),
			'height' => array(),
			'controls' => array(),
			'label' => array(),
			'title' => array()				
			);
	
	/**
	 * Sets the default map properties and returns the new array.
	 * This function also ensures all the properties are present, even when being empty,
	 * which is important for the weakly typed classes using them.
	 *
	 * @param array $params Array containing the current set of pareters.
	 * @param array $serviceDefaults Array with the default parameters and their values for the used mapping service.
	 * @param boolean $strict If set to false, values which a key that does not
	 * exist in the $map array will be retained.
	 * @return array
	 */
	public static function setDefaultParValues(array $params, array $serviceDefaults, $strict = true) {
		global $egMapsMapLat, $egMapsMapLon, $egMapsMapWidth, $egMapsMapHeight, $egMapsDefaultService;

        $mapDefaults = array(
            'service' => $egMapsDefaultService,
            'coordinates' => "$egMapsMapLat, $egMapsMapLon",
        	'zoom' => '',
        	'centre' => '',
            'width' => $egMapsMapWidth,
            'height' => $egMapsMapHeight,  
        	'controls' => array(),
        	'title' => '',
        	'label' => ''
            );	

        $map = array_merge($mapDefaults, $serviceDefaults);
		
		foreach($params as $paramName => $paramValue) {
			if(array_key_exists($paramName, $map) || !$strict) $map[$paramName] = $paramValue;
		}
		
		return $map;
	}
	
	/**
	 * Returns the JS version (true/false as string) of the provided boolean parameter.
	 *
	 * @param boolean $bool
	 * @return string
	 */
	public static function getJSBoolValue($bool) {		
		return $bool ? 'true' : 'false';
	}	
	
	/**
	 * Turns the provided values into an array by splitting it on comma's if
	 * it's not an array yet.
	 *
	 * @param unknown_type $values
	 * @param string $delimeter
	 */
	public static function enforceArrayValues(&$values, $delimeter = ',') {
		if (!is_array($values)) $values = split($delimeter, $values); // If not an array yet, split the values
		for ($i = 0; $i < count($values); $i++) $values[$i] = trim($values[$i]); // Trim all values
	}
	
	/**
	 * Checks if the items array has members, and sets it to the default when this is not the case.
	 * Then returns an imploded/joined, comma seperated, version of the array as string.
	 *
	 * @param array $items
	 * @param array $defaultItems
	 * @param boolean $asStrings
	 * @return string
	 */
	public static function createJSItemsString(array $items, array $defaultItems = null, $asStrings = true, $toLower = true) {
		if (count($items) < 1 && isset($defaultItems)) $items = $defaultItems;
		$itemString = $asStrings ? "'" . implode("','", $items) . "'" : implode(",", $items);
		if ($toLower) $itemString = strtolower($itemString);
		return $itemString;
	}		
	
	/**
	 * Returns a valid version of the provided parameter array. Paramaters that are not allowed will
	 * be ignored, and alias parameter names will be changed to main parameter names, using getMainParamName().
	 *
	 * @param array $paramz
	 * @param array $serviceParameters
	 * @return array
	 */
	public static function getValidParams(array $paramz, array $serviceParameters) {
		$validParams = array();
		
		$allowedParms = array_merge(self::$mainParams, $serviceParameters);
		
		foreach($paramz as $paramName => $paramValue) {		
			//echo "$paramName ->into-> ";
			$paramName = self::getMainParamName($paramName, $allowedParms);
			//echo "$paramName ,withval, "; var_dump($paramValue); echo " <br />\n";
			if(array_key_exists($paramName, $allowedParms)) $validParams[$paramName] = $paramValue;
		}
		
		return $validParams;		
	}
	
	/**
	 * Checks if the patameter name is an alias for an actual parameter,
	 * and changes it into the main paremeter name if this is the case.
	 *
	 * @param string $paramName
	 * @param array $allowedParms
	 * @return string
	 */
	private static function getMainParamName($paramName, array $allowedParms) {
		//echo "$paramName -> ";
		if (!array_key_exists($paramName, $allowedParms)) {
			foreach ($allowedParms as $name => $aliases) {
				if (in_array($paramName, $aliases)) {
					$paramName = $name;
				}
			}
		}
		//echo "$paramName<br />";
		return $paramName;
	}		
		
	/**
	 * Returns a valid service. When an invalid service is provided, the default one will be returned.
	 * Aliases are also chancged into the main service names @see MapsMapper::getMainServiceName().
	 *
	 * @param string $service
	 * @return string
	 */
	public static function getValidService($service) {
		global $egMapsAvailableServices, $egMapsDefaultService;
		
		$service = self::getMainServiceName($service);
		if(!in_array($service, $egMapsAvailableServices)) $service = $egMapsDefaultService;
		
		return $service;
	}
	
	/**
	 * Checks if the service name is an alias for an actual service,
	 * and changes it into the main service name if this is the case.
	 *
	 * @param string $service
	 * @return string
	 */
	public static function getMainServiceName($service) {
		global $egMapsServices;
		
		if (!array_key_exists($service, $egMapsServices)) {
			foreach ($egMapsServices as $serviceName => $serviceInfo) {
				if (in_array($service, $serviceInfo['aliases'])) {
					 $service = $serviceName;
				}
			}
		}
		
		return $service;
	}
}
