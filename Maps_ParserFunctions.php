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

	private static function getMapHtml(array $params, array $coordFails = array()) {
		global $egMapsServices;
		
		$map = array();
		
		foreach($params as $param) {
			$split = split('=', $param);
			if (count($split) == 2) {
				$paramName = strtolower(trim($split[0]));
				$paramValue = trim($split[1]);
				$map[$paramName] = $paramValue;
			}
			else if (count($split) == 1) { // Default parameter (without name)
				$map['coordinates'] = trim($split[0]);
			}
		}
		
		$coords = MapsMapper::getParamValue('coordinates', $map);
		
		if ($coords) {
			if (! MapsMapper::paramIsPresent('service', $map)) $map['service'] = '';
			$map['service'] = MapsMapper::getValidService($map['service'], 'pf');				
	
			$mapClass = new $egMapsServices[$map['service']]['pf']['class']();
	
			// Call the function according to the map service to get the HTML output
			$output = $mapClass->displayMap($map);	
			
			if (count($coordFails) > 0) {
				$output .= '<i>' . wfMsgExt( 'maps_geocoding_failed_for', array( 'parsemag' ), implode( ',', $coordFails ), count( $coordFails ) ) . '</i>';
			}
		}
		elseif (trim($coords) == "" && count($coordFails) > 0) {
			$output = '<i>' . wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), implode(',', $coordFails), count( $coordFails ) ) . '</i>';
		}
		else {
			$output = '<i>'.wfMsg( 'maps_coordinates_missing' ).'</i>';
		}
		
		// Return the result
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}

	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayPointRender(&$parser) {		
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
				
		return self::getMapHtml($params);
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
		
		return self::getMapHtml($params);
	}
	
	/**
	 * Turns the address parameter into coordinates, then calls
	 * getMapHtml() and returns it's result. 
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayAddressRender(&$parser) {		
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		$fails = self::changeAddressToCoords($params);
		
		return self::getMapHtml($params, $fails);
	}
	
	/**
	 * Turns the address parameter into coordinates, then calls
	 * getMapHtml() and returns it's result. 
	 *
	 * @param unknown_type $parser
	 */
	public static function displayAddressesRender(&$parser) {
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		$fails = self::changeAddressToCoords($params);
		
		return self::getMapHtml($params, $fails);
	}
	
	/**
	 * Changes the values of the address or addresses parameter into coordinates
	 * in the provided array. Returns an array containing the addresses that
	 * could not be geocoded.
	 *
	 * @param array $params
	 */
	private static function changeAddressToCoords(&$params) {
		global $egMapsDefaultService;

		$fails = array();
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (MapsMapper::inParamAliases(strtolower(trim($split[0])), 'service') && count($split) > 1) {
				$service = trim($split[1]);
			}
			else if (strtolower(trim($split[0])) == 'geoservice' && count($split) > 1) {
				$geoservice = trim($split[1]);
			}			
		}

		$service = isset($service) ? MapsMapper::getValidService($service, 'pf') : $egMapsDefaultService;

		$geoservice = isset($geoservice) ? $geoservice : '';
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (((strtolower(trim($split[0])) == 'address' || strtolower(trim($split[0])) == 'addresses') && count($split) > 1) || count($split) == 1) {
				$address_srting = count($split) == 1 ? $split[0] : $split[1];
				
				$addresses = explode(';', $address_srting);
				
				$coordinates = array();
				
				foreach($addresses as $address) {
					$args = explode('~', $address);
					$coords =  MapsGeocoder::geocodeToString(trim($args[0]), $geoservice, $service);
					
					if ($coords) {
						$args[0] = $coords;
						$coordinates[] = implode('~', $args);
					}
					else {
						$fails[] = $args[0];
					}
				}				
				
				$params[$i] = 'coordinates=' . implode(';', $coordinates);

			}
		}

		return $fails;
	}

}