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
	 * Sets the default map properties and returns the new array.
	 *
	 * @param array $params Array containing the current set of pareters.
	 * @param boolean $strict If set to false, values which a key that does not
	 * exist in the $map array will be retained.
	 * @return unknown
	 */
	public static function setDefaultParValues(array $params, $strict) {
		global $egMapsMapCoordinates, $egMapsMapWidth, $egMapsMapHeight, $egMapsEnableAutozoom, $egMapsEnableEarth;
		
		$map = array(
			'service' => '',
			'coordinates' => $egMapsMapCoordinates,
			'zoom' => '',
			'centre' => '',	
			'type' => '',
			'width' => "$egMapsMapWidth",
			'height' => "$egMapsMapHeight",
			'controls' => array(),
			'autozoom' => $egMapsEnableAutozoom ? 'on' : 'off',
			'earth' => $egMapsEnableEarth ? 'on' : 'off',
			'class' => 'pmap',
			'layers' => array(),	
			'baselayer' => '',
			'title' => '',
			'label' => '',
			'style' => ''	
			);
		
		foreach($params as $paramName => $paramValue) {
			if(array_key_exists($paramName, $map) || !$strict) $map[$paramName] = $paramValue;
		}
		
		// Alias for centre
		if(array_key_exists('center', $params)) $map['centre'] = $params['center'];
		
		self::enforceArrayValues($map['controls']);
		self::enforceArrayValues($map['layers']);
		
		return $map;
	}
	
	public static function enforceArrayValues(&$values, $delimeter = ',') {
		if (!is_array($values)) $values = split($delimeter, $values); // If not an array yet, split the values
		for ($i = 0; $i < count($values); $i++) $values[$i] = trim($values[$i]); // Trim all values
	}
	
	public static function createControlsString(array $controls, array $defaultControls) {
		if (count($controls) < 1) $controls = $defaultControls;
		$controlItems = '';
		foreach ($controls as $control) $controlItems .= "'$control'" . ',';
		
		return strtolower(rtrim($controlItems, ','));
	}			
	
	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 * @return unknown
	 */
	public static function displayPointRender(&$parser) {
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
		
		$map = self::setDefaultParValues($map, true);

		$map['service'] = self::getValidService($map['service']);

		
		switch($map['service']) {
			case 'openlayers' :
				$mapClass = new MapsOpenLayers();
				break;
			case 'yahoomaps' : 
				$mapClass = new MapsYahooMaps();
				break;	
			default:
				$mapClass = new MapsGoogleMaps();
				break;
		}

		// Call the function according to the map service to get the HTML output
		$output = $mapClass->displayMap($parser, $map);
		
		// Return the result
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

	/**
	 * Turns the address parameter into coordinates, then lets 
	 * @see MapsMapper::displayPointRender() do the work and returns it.
	 *
	 * @param unknown_type $parser
	 * @return unknown
	 */
	public static function displayAddressRender(&$parser) {		
		// TODO: refactor to reduce redundancy and improve performance
		global $egMapsDefaultService;
		
		$params = func_get_args();
		array_shift( $params ); // we already know the $parser ...
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (strtolower(trim($split[0])) == 'service' && count($split) > 1) {
				$service = trim($split[1]);
				//echo "<!-- ||| $service -->";
			}
		}

		
		$service = isset($service) ? MapsMapper::getValidService($service) : $egMapsDefaultService;
		
		$geoservice = '';
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (strtolower(trim($split[0])) == 'geoservice' && count($split) > 1) {
				$geoservice = trim($split[1]);
			}
		}	
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (strtolower(trim($split[0])) == 'address' && count($split) > 1) {
				$params[$i] = 'coordinates=' . MapsGeocoder::renderGeocoder($parser, trim($split[1]), $geoservice, $service);
			}
			if (count($split) == 1) { // Default parameter (without name)
				$params[$i] = 'coordinates=' . MapsGeocoder::renderGeocoder($parser, trim($split[0]), $geoservice, $service);
			}
		}
		
		return self::displayPointRender($parser, $params);
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
	 * Checks if the service name is an allias for an actual service,
	 * and changes it into the main service name if this is the case.
	 *
	 * @param unknown_type $service
	 * @return unknown
	 */
	public static function getMainServiceName($service) {
		global $egMapsServices;
		
		if (!array_key_exists($service, $egMapsServices)) {
			foreach ($egMapsServices as $serviceName => $aliasList) {
				if (in_array($service, $aliasList)) {
					 $service = $serviceName;
				}
			}
		}
		
		return $service;
	}
}
