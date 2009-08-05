<?php

/**
 * A class that holds handlers for the mapping parser functions
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
	 * @return unknown
	 */
	public static function setDefaultParValues(array $params, array $serviceDefaults, $strict = true) {
		global $egMapsMapLat, $egMapsMapLon, $egMapsMapWidth, $egMapsMapHeight, $egMapsDefaultZoom, $egMapsDefaultService;
		
        $mapDefaults = array(
            'service' => $egMapsDefaultService,
            'coordinates' => "$egMapsMapLat, $egMapsMapLon",
        	'zoom' => $egMapsDefaultZoom,
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
	 * Returns a valid version of the provided parameter array. Paramaters that are not allowed will
	 * be ignored, and alias parameter names will be changed to main parameter names, using getMainParamName().
	 *
	 * @param unknown_type $params
	 * @param unknown_type $serviceParameters
	 * @return unknown
	 */
	public static function getValidParams($params, $serviceParameters) {
		$validParams = array();
		
		$allowedParms = array_merge(self::$mainParams, $serviceParameters);
		
		foreach($params as $paramName => $paramValue) {
			$paramName = self::getMainParamName($paramName, $allowedParms);
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
		if (!array_key_exists($paramName, $allowedParms)) {
			foreach ($allowedParms as $name => $aliases) {
				if (in_array($paramName, $aliases)) $paramName = $name;
			}
		}
		
		return $paramName;
	}
	
	/**
	 * Turns the provided values into an array by splitting it on comma's if
	 * it's not an array yet.
	 *
	 * @param unknown_type $values
	 * @param unknown_type $delimeter
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
	 * @return unknown
	 */
	public static function createJSItemsString(array $items, array $defaultItems) {
		if (count($items) < 1) $items = $defaultItems;
		return "'" . strtolower(implode("','", $items)) . "'";
	}			
	
	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 * @return unknown
	 */
	public static function displayPointRender(&$parser) {
		global $egMapsServices;
		
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		if (is_array($params[0])) $params = $params[0];
				
		$map = array();
		
		foreach($params as $param) {
			$split = split('=', $param);
			if (count($split) == 2) {
				$paramName = strtolower(trim($split[0]));
				$paramValue = trim($split[1]);
				$map[$paramName] = $paramValue;
			}
			if (count($split) == 1) { // Default parameter (without name)
				$map['coordinates'] = trim($split[0]);
			}
		}
		
		$map['coordinates'] = explode(';', $map['coordinates']);
		
		$map['service'] = self::getValidService($map['service']);
		
		$map = self::setDefaultParValues($map,  $egMapsServices[$map['service']]['parameters'], true);

		$mapClass = new $egMapsServices[$map['service']]['pf']['class']();
		
		// Call the function according to the map service to get the HTML output
		$output = $mapClass->displayMap($parser, $map);
		
		// Return the result
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 */
	public static function displayPointsRender(&$parser) {
		return self::displayPointRender(func_get_args());
	}
	
	/**
	 * Turns the address parameter into coordinates, then lets 
	 * @see MapsMapper::displayPointRender() do the work and returns it.
	 *
	 * @param unknown_type $parser
	 * @return unknown
	 */
	public static function displayAddressRender(&$parser) {		
		global $egMapsDefaultService;
		
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		self::changeAddressToCoords($params);
		
		return self::displayPointRender($parser, $params);
	}
	
	/**
	 * Turns the address parameter into coordinates, then lets 
	 * @see MapsMapper::displayPointRender() do the work and returns it.
	 *
	 * @param unknown_type $parser
	 */
	public static function displayAddressesRender(&$parser) {	
		return self::displayAddressRender(func_get_args());
	}
	
	/**
	 * Changes the values of the address or addresses parameter into coordinates
	 * in the provided array.
	 *
	 * @param array $params
	 */
	private static function changeAddressToCoords(&$params) {
		global $egMapsDefaultService;

		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (strtolower(trim($split[0])) == 'service' && count($split) > 1) {
				$service = trim($split[1]);
			}
			else if (strtolower(trim($split[0])) == 'geoservice' && count($split) > 1) {
				$geoservice = trim($split[1]);
			}			
		}

		$service = isset($service) ? MapsMapper::getValidService($service) : $egMapsDefaultService;
		$geoservice = isset($geoservice) ? $geoservice : '';
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (((strtolower(trim($split[0])) == 'address' || strtolower(trim($split[0])) == 'addresses') && count($split) > 1) || count($split) == 1) {
				$address_srting = count($split) == 1 ? $split[0] : $split[1];
				
				$addresses = explode(';', $address_srting);
				
				$coordinates = array();
				
				foreach($addresses as $address) {
					$coordinates[] = MapsGeocoder::renderGeocoder(null, trim($address), $geoservice, $service);
				}				
				
				$params[$i] = 'coordinates=' . implode(';', $coordinates);
			}
		}		
	}
	
	/**
	 * Returns a valid service. When an invalid service is provided, the default one will be returned.
	 * Aliases are also chancged into the main service names @see MapsMapper::getMainServiceName().
	 *
	 * @param unknown_type $service
	 * @return unknown
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
	 * @param unknown_type $service
	 * @return unknown
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
