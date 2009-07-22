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
		global $egMapsMapCoordinates, $egMapsMapZoom, $egMapsMapWidth, $egMapsMapHeight, $egMapsEnableAutozoom, $egMapsEnableEarth;
		
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
			'style' => ''	
			);
		
		foreach($params as $paramName => $paramValue) {
			if(array_key_exists($paramName, $map) || !$strict) $map[$paramName] = $paramValue;
		}
		
		// Alias for centre
		if(array_key_exists('center', $params)) $map['centre'] = $params['center'];
		
		MapsMapper::enforceArrayValues($map['controls']);
		MapsMapper::enforceArrayValues($map['layers']);
		
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
	 * Sets the default map properties and then builds up the HTML depending
	 * on the chosen map service and the provided map properties.
	 *
	 * @param unknown_type $parser
	 * @return unknown
	 */
	public static function displayPointRender(&$parser) {
		global $egMapsDefaultService, $egMapsAvailableServices;
		
		$params = func_get_args();
		array_shift( $params ); // we already know the $parser ...
		
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
		
		$map = MapsMapper::setDefaultParValues($map, true);

		// Call the function according to the map service to get the HTML output
		if(!in_array($map['service'], $egMapsAvailableServices)) $map['service'] = $egMapsDefaultService;

		switch($map['service']) {
			case 'openlayers' : case 'layers' : 
				$output = MapsOpenLayers::displayMap($parser, $map);
				break;
			case 'yahoomaps' : case 'yahoo' : 
				$output = MapsYahooMaps::displayMap($parser, $map);
				break;	
			default:
				$output = MapsGoogleMaps::displayMap($parser, $map);
				break;
		}

		// Return the result
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

	public static function displayAddressRender(&$parser) {		
		$params = func_get_args();
		array_shift( $params ); // we already know the $parser ...
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (strtolower(trim($split[0])) == 'address' && count($split) > 1) {
				$params[$i] = 'coordinates=' . MapsGeocoder::renderGeocoder($parser, trim($split[1]));
			}
			if (count($split) == 1) { // Default parameter (without name)
				$params[$i] = 'coordinates=' . MapsGeocoder::renderGeocoder($parser, trim($split[0]));
			}
		}
		
		return MapsMapper::displayPointRender($parser, $params);
	}
}
