<?php

/**
 * A class that holds handlers for the mapping parser functions.
 * Spesific functions are located in @see MapsUtils
 *
 * @file Maps_ParserFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsParserFunctions {

	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 * @return array
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
		
		$map['service'] = MapsMapper::getValidService($map['service']);				
		
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
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		if (is_array($params[0])) $params = $params[0];
		
		return self::displayPointRender($parser, $params);
	}
	
	/**
	 * Turns the address parameter into coordinates, then lets 
	 * @see MapsMapper::displayPointRender() do the work and returns it.
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayAddressRender(&$parser) {		
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
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		self::changeAddressToCoords($params);
		
		return self::displayPointsRender($parser, $params);
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

}