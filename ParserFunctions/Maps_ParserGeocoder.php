<?php

/**
 * 
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
	public static function changeAddressToCoords(&$params) {
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
			$isAddress = ((strtolower(trim($split[0])) == 'address' || strtolower(trim($split[0])) == 'addresses') && count($split) > 1);
			
			if ($isAddress || count($split) == 1) {
				$address_srting = count($split) == 1 ? $split[0] : $split[1];
				$addresses = explode(';', $address_srting);

				$coordinates = array();
				
				foreach($addresses as $address) {
					$args = explode('~', $address);
					$args[0] = trim($args[0]);
					
					if (strlen($args[0]) > 0) {
						$coords =  MapsGeocoder::geocodeToString($args[0], $geoservice, $service);
						
						if ($coords) {
							$args[0] = $coords;
							$coordinates[] = implode('~', $args);
						}
						else {
							$fails[] = $args[0];
						}
					}
				}				
				
				$params[$i] = 'coordinates=' . implode(';', $coordinates);

			}
		}

		return $fails;
	}	
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function attemptToGeocode($coordsOrAddress) {
		// TODO: add check for DM and DD notations
		$floatRegex = "/^\d{1,3}(|\.\d{1,7}),(|\s)\d{1,3}(|\.\d{1,7})$/";
		$dmsRegex = "/^(\d{1,2}°)(\d{2}')?((\d{2}\")?|(\d{2}\.\d{2}\")?)(N|S)(| )(\d{1,2}°)(\d{2}')?((\d{2}\")?|(\d{2}\.\d{2}\")?)(E|W)$/";
		
		$needsGeocoding = !preg_match($floatRegex, $coordsOrAddress);
		if ($needsGeocoding) $needsGeocoding = !preg_match($dmsRegex, $coordsOrAddress);
		
		if ($needsGeocoding) {
			// TODO: geocode
		}
		
		return array('coords' => $coords, 'geocoded' => $needsGeocoding);
	}	
	
}