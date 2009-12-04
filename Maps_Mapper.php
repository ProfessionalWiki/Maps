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
	 * Array holding the parameters that are not spesific to a mapping service, 
	 * their aliases, criteria and default value.
	 *
	 * @var array
	 */
	private static $mainParams;

	public static function initializeMainParams() {
		global $egMapsSizeRestrictions, $egMapsMapWidth, $egMapsMapHeight;

		self::$mainParams = array
			(
			'zoom' => array(
				'aliases' => array(),
				'criteria' => array(
					'is_numeric' => array(),
					'in_range' => array(0, 15)
					)			
				),
			'width' => array(
				'aliases' => array(),
				'criteria' => array(
					'is_numeric' => array(),
					'in_range' => $egMapsSizeRestrictions['width']
					),
				'default' => $egMapsMapWidth		
				),
			'height' => array(
				'aliases' => array(),
				'criteria' => array(
					'is_numeric' => array(),
					'in_range' => $egMapsSizeRestrictions['height']
					),
				'default' => $egMapsMapHeight
				),
			'controls' => array(
				'aliases' => array(),
				'criteria' => array(),					
				),
			);
	}

	/**
	 * Returns the main parameters array.
	 * 
	 * @return array
	 */
	public static function getMainParams() {
		return self::$mainParams;
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
		if (! is_array($values)) $values = explode($delimeter, $values); // If not an array yet, split the values
		for ($i = 0; $i < count($values); $i++) $values[$i] = trim($values[$i]); // Trim all values
	}
	
	/**
	 * Checks if the items array has members, and sets it to the default when this is not the case.
	 * Then returns an imploded/joined, comma seperated, version of the array as string.
	 *
	 * @param array $items
	 * @param array $defaultItems
	 * @param boolean $asStrings
	 * @param boolean $toLower
	 *  
	 * @return string
	 */
	public static function createJSItemsString(array $items, $asStrings = true, $toLower = true) {
		$itemString = $asStrings ? "'" . implode("','", $items) . "'" : implode(',', $items);
		if ($toLower) $itemString = strtolower($itemString);
		return $itemString;
	}
	
	/**
	 * Returns a valid version of the types.
	 *
	 * @param array $types
	 * @param array $defaults
	 * @param boolean $defaultsAreValid
	 * @param function $validationFunction
	 * 
	 * @return array
	 */
	public static function getValidTypes(array $types, array &$defaults, &$defaultsAreValid, $validationFunction) {
		$validTypes = array();
		$phpAtLeast523 = MapsUtils::phpVersionIsEqualOrBigger('5.2.3');
		
		// Ensure every type is valid and uses the relevant map API's name.
		for($i = 0 ; $i < count($types); $i++) {
			$type = call_user_func($validationFunction, $phpAtLeast523 ? $types[$i] : array($types[$i]));
			if ($type) $validTypes[] = $type; 
		}
		
		$types = $validTypes;
		
		// If there are no valid types, add the default ones.
		if (count($types) < 1) {
			$validTypes = array();
			
			// If the default ones have not been checked,
			// ensure every type is valid and uses the relevant map API's name.
			if (empty($defaultsAreValid)) {
				for($i = 0 ; $i < count($defaults); $i++) {
					$type = call_user_func($validationFunction, $phpAtLeast523 ? $defaults[$i] : array($defaults[$i]));
					if ($type) $validTypes[] = $type; 
				}
				
				$defaults = $validTypes;
				$defaultsAreValid = true;
			}
			
			$types = $defaults;
		}

		return $types;
	}
	
	/**
	 * Returns a valid service. When an invalid service is provided, the default one will be returned.
	 * Aliases are also chancged into the main service names @see MapsMapper::getMainServiceName().
	 *
	 * @param string $service
	 * @param string $feature
	 * 
	 * @return string
	 */
	public static function getValidService($service, $feature) {
		global $egMapsAvailableServices, $egMapsDefaultService, $egMapsDefaultServices, $egMapsServices;
		
		$service = self::getMainServiceName($service);
		
		$shouldChange = ! array_key_exists($service, $egMapsServices);
		if (! $shouldChange) $shouldChange = ! array_key_exists($feature, $egMapsServices[$service]);
		
		if ($shouldChange) {
			$service = array_key_exists($feature, $egMapsDefaultServices) ? $egMapsDefaultServices[$feature] : $egMapsDefaultService;
		}
		
		if(! in_array($service, $egMapsAvailableServices)) $service = $egMapsDefaultService;
		
		return $service;
	}
	
	/**
	 * Checks if the service name is an alias for an actual service,
	 * and changes it into the main service name if this is the case.
	 *
	 * @param string $service
	 * @return string
	 */
	private static function getMainServiceName($service) {
		global $egMapsServices;
		
		if (! array_key_exists($service, $egMapsServices)) {
			foreach ($egMapsServices as $serviceName => $serviceInfo) {
				if (in_array($service, $serviceInfo['aliases'])) {
					 $service = $serviceName;
					 break;
				}
			}
		}
		
		return $service;
	}	
}
