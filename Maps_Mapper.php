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
		global $egMapsAvailableServices, $egMapsDefaultService, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsDefaultCentre;
		global $egMapsSizeRestrictions, $egMapsMapWidth, $egMapsMapHeight, $egMapsDefaultTitle, $egMapsDefaultLabel;
		
		self::$mainParams = array
			(		
			'service' => array(
				'aliases' => array(),
				'criteria' => array(
					'in_array' => $egMapsAvailableServices
					),
				'default' => $egMapsDefaultService
				),			
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
			'lang' => array(
				'aliases' => array('locale', 'language'),	
				'criteria' => array(
					'not_empty' => array()
					),
				'default' => ''					
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
	 * Gets if a provided name is present in the aliases array of a parameter
	 * name in the $mainParams array.
	 *
	 * @param string $name The name you want to check for.
	 * @param string $mainParamName The main parameter name.
	 * @param boolean $compareMainName Boolean indicating wether the main name should also be compared.
	 * 
	 * @return boolean
	 */
	public static function inParamAliases($name, $mainParamName, $compareMainName = true) {
		$equals = $compareMainName && $mainParamName == $name;

		if (array_key_exists($mainParamName, self::$mainParams)) {
			$equals = $equals || in_array($name, self::$mainParams[$mainParamName]);
		}

		return $equals;
	}	
	
    /**
     * Gets if a parameter is present as key in the $stack. Also checks for
     * the presence of aliases in the $mainParams array unless specified not to.
     *
     * @param string $paramName
     * @param array $stack
     * @param boolean $checkForAliases
     * 
     * @return boolean
     */        
    public static function paramIsPresent($paramName, array $stack, $checkForAliases = true) {
        $isPresent = array_key_exists($paramName, $stack);
        
        if ($checkForAliases) {
            foreach(self::$mainParams[$paramName]['aliases'] as $alias) {
                if (array_key_exists($alias, $stack)) {
                	$isPresent = true;
                	break;
                }
            }
        }

        return $isPresent;
    }
	
	/**
	 * Returns the value of a parameter represented as key in the $stack.
	 * Also checks for the presence of aliases in the $mainParams array
	 * and returns the value of the alies unless specified not to. When
	 * no array key name match is found, false will be returned.
	 *
	 * @param string $paramName
	 * @param array $stack The values to search through
	 * @param array $paramInfo Contains meta data, including aliases, of the possible parameters
	 * @param boolean $checkForAliases
	 * 
	 * @return the parameter value or false
	 */
	public static function getParamValue($paramName, array $stack, array $paramInfo = array(), $checkForAliases = true) {
		$paramValue = false;
		
		if (array_key_exists($paramName, $stack)) $paramValue = $stack[$paramName];
		
		if ($checkForAliases) {
			foreach($paramInfo[$paramName]['aliases'] as $alias) {
				if (array_key_exists($alias, $stack)) $paramValue = $stack[$alias];
				break;
			}
		}
		
		return $paramValue;		
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
	 * Returns the main parameter name for a given parameter or alias, or false
	 * when it is not recognized as main parameter or alias.
	 *
	 * @param string $paramName
	 * @param array $allowedParms
	 * 
	 * @return string
	 */
	public static function getMainParamName($paramName, array $allowedParms) {
		$result = false;
		
		if (array_key_exists($paramName, $allowedParms)) {
			$result = $paramName;
		}
		else {
			foreach ($allowedParms as $name => $data) {
				if (array_key_exists('aliases', $data)) {
					if (in_array($paramName, $data['aliases'])) {
						$result = $name;
						break;
					}					
				}
			}
		}
		
		return $result;
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
	public static function getMainServiceName($service) {
		global $egMapsServices;
		
		if (! array_key_exists($service, $egMapsServices)) {
			foreach ($egMapsServices as $serviceName => $serviceInfo) {
				if (in_array($service, $serviceInfo['aliases'])) {
					 $service = $serviceName;
				}
			}
		}
		
		return $service;
	}
	
	/**
	 * Returns a valid version of the types.
	 *
	 * @param array $types
	 * @param array $defaults
	 * @param boolean $defaultsAreValid
	 * @param function $validationFunction
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
	

}
