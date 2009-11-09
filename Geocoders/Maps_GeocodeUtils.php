<?php

/**
 * MapsGeocodeUtils holds static functions to geocode values when needed.
 *
 * @file Maps_GeocodeUtils.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGeocodeUtils {
	/**
	 * This function first determines wether the provided string is a pair or coordinates 
	 * or an address. If it's the later, an attempt to geocode will be made. The function will
	 * return the coordinates or false, in case a geocoding attempt was made but failed. 
	 * 
	 * @param $coordsOrAddress
	 * @param $geoservice
	 * @param $service
	 * 
	 * @return string or boolean
	 */
	public static function attemptToGeocode($coordsOrAddress, $geoservice, $service) {
		if (MapsGeocodeUtils::isCoordinate($coordsOrAddress)) {
			$coords = $coordsOrAddress;
		}
		else {
			$coords = MapsGeocoder::geocodeToString($coordsOrAddress, $geoservice, $service);
		}
		
		return $coords;
	}	
	
	/**
	 * Returns a boolean indication if a provided value is a valid coordinate.
	 * 
	 * @param string $coordsOrAddress
	 * 
	 * @return boolean
	 */
	private static function isCoordinate($coordsOrAddress) {
		$coordRegexes = array(
			'/^(-)?\d{1,3}(\.\d{1,7})?,(\s)?(-)?\d{1,3}(\.\d{1,7})?$/', // Floats
			'/^(\d{1,2}°)(\d{2}\′)?((\d{2}″)?|(\d{2}\.\d{2}″)?)(N|S)(\s)?(\d{1,2}°)(\d{2}\′)?((\d{2}″)?|(\d{2}\.\d{2}″)?)(E|W)$/', // DMS 
			'/^(-)?\d{1,3}(|\.\d{1,7})°,(\s)?(-)?(\s)?\d{1,3}(|\.\d{1,7})°$/', // DD
			'/(-)?\d{1,3}°\d{1,3}(\.\d{1,7}\')?,(\s)?(-)?\d{1,3}°\d{1,3}(\.\d{1,7}\')?$/', // DM
			);
			
		$isCoordinate = false;
		
		foreach ($coordRegexes as $coordRegex) {
			if (preg_match($coordRegex, $coordsOrAddress)) {
				$isCoordinate = true;
				continue;
			}		
		}

		return $isCoordinate;
	}	
	
}