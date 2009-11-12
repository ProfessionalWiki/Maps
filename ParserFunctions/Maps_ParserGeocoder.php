<?php

/**
 * File holding the MapsParserGeocoder class.
 *
 * @file Maps_ParserGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class that holds static helpers for the mapping parser functions. The helpers aid in 
 * determining the availability of the geocoding parser functions and calling them.
 * 
 * @author Jeroen De Dauw
 *
 */
final class MapsParserGeocoder {
	
	/**
	 * Changes the values of the address or addresses parameter into coordinates
	 * in the provided array. Returns an array containing the addresses that
	 * could not be geocoded.
	 *
	 * @param array $params
	 */
	public static function changeAddressesToCoords(&$params) {
		global $egMapsDefaultService;

		$fails = array();
		
		// Get the service and geoservice from the parameters, since they are needed to geocode addresses.
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (MapsMapper::inParamAliases(strtolower(trim($split[0])), 'service') && count($split) > 1) {
				$service = trim($split[1]);
			}
			else if (strtolower(trim($split[0])) == 'geoservice' && count($split) > 1) {
				$geoservice = trim($split[1]);
			}			
		}

		// Make sure the service and geoservice are valid.
		$service = isset($service) ? MapsMapper::getValidService($service, 'pf') : $egMapsDefaultService;
		if (! isset($geoservice)) $geoservice = '';
		
		// Go over all parameters.
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			$isAddress = (strtolower(trim($split[0])) == 'address' || strtolower(trim($split[0])) == 'addresses') && count($split) > 1;
			$isDefault = count($split) == 1;
			
			// If a parameter is either the default (no name), or an addresses list, extract all locations.
			if ($isAddress || $isDefault) {
				
				$address_srting = $split[count($split) == 1 ? 0 : 1];
				$addresses = explode(';', $address_srting);

				$coordinates = array();
				
				// Go over every location and attempt to geocode it.
				foreach($addresses as $address) {
					$args = explode('~', $address);
					$args[0] = trim($args[0]);
					
					if (strlen($args[0]) > 0) {
						$coords =  MapsGeocodeUtils::attemptToGeocode($args[0], $geoservice, $service, $isDefault);
						
						if ($coords) {
							$args[0] = $coords;
							$coordinates[] = implode('~', $args);
						}
						else {
							$fails[] = $args[0];
						}
					}
				}				
				
				// Add the geocoded result back to the parameter list.
				$params[$i] = implode(';', $coordinates);

			}
			
		}

		return $fails;
	}	
		
}