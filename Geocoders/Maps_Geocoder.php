<?php

/** 
 * Geocoding class. Provides methods to geocode a string to a pair of coordinates
 * using one of the available geocoding services.
 *
 * @file Maps_Geocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Sergey Chernyshev
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGeocoder {
	
	/**
	 * The geocoder cache, holding geocoded data when enabled.
	 *
	 * @var array
	 */
	private static $mGeocoderCache = array();
	
	/**
	 * This function first determines wether the provided string is a pair or coordinates 
	 * or an address. If it's the later, an attempt to geocode will be made. The function will
	 * return the coordinates or false, in case a geocoding attempt was made but failed. 
	 * 
	 * @param string $coordsOrAddress
	 * @param string $geoservice
	 * @param string $service
	 * @param boolean $checkForCoords
	 * 
	 * @return array or false
	 */
	public static function attemptToGeocode( $coordsOrAddress, $geoservice, $service, $checkForCoords = true ) {
		if ( $checkForCoords ) {			
			if ( MapsCoordinateParser::areCoordinates( $coordsOrAddress ) ) {
				return MapsCoordinateParser::parseCoordinates( $coordsOrAddress );
			} else {
				return self::geocode( $coordsOrAddress, $geoservice, $service );
			}
		} else {
			return self::geocode( $coordsOrAddress, $geoservice, $service );
		}
	}
	
	/**
	 * Geocodes an address with the provided geocoding service and returns the result 
	 * as a string with the optionally provided format, or false when the geocoding failed.
	 * 
	 * @param string $coordsOrAddress
	 * @param string $service
	 * @param string $mappingService
	 * @param coordinate type $targetFormat The notation to which they should be formatted. Defaults to floats.
	 * @param boolean $directional Indicates if the target notation should be directional. Defaults to false.
	 * 
	 * @return formatted coordinate string or false
	 */
	public static function attemptToGeocodeToString( $coordsOrAddress, $service = '', $mappingService = false, $checkForCoords = true, $targetFormat = Maps_COORDS_FLOAT, $directional = false ) {
		$geoValues = self::attemptToGeocode( $coordsOrAddress, $service, $mappingService, $checkForCoords );
		return $geoValues ?  MapsCoordinateParser::formatCoordinates( $geoValues, $targetFormat, $directional ) : false;
	}

	/**
	 * Geocodes an address with the provided geocoding service and returns the result 
	 * as an array, or false when the geocoding failed.
	 *
	 * @param string $address
	 * @param string $service
	 * @param string $mappingService
	 * 
	 * @return array with coordinates or false
	 */
	public static function geocode( $address, $service, $mappingService ) {
		global $egMapsGeoServices, $wgAutoloadClasses, $egMapsDir, $IP, $egMapsEnableGeoCache;
		
		// If the adress is already in the cache and the cache is enabled, return the coordinates.
		if ( $egMapsEnableGeoCache && array_key_exists( $address, MapsGeocoder::$mGeocoderCache ) ) {
			return self::$mGeocoderCache[$address];
		}
		
		$service = self::getValidGeoService( $service, $mappingService );
		
		// Call the geocode function in the spesific geocoder class.
		$coordinates = call_user_func( array( $egMapsGeoServices[$service], 'geocode' ), $address );

		// Add the obtained coordinates to the cache when there is a result and the cache is enabled.
		if ( $egMapsEnableGeoCache && $coordinates ) {
			MapsGeocoder::$mGeocoderCache[$address] = $coordinates;
		}

		return $coordinates;
	}
	
	/**
	 * Makes sure that the geo service is one of the available ones.
	 * Also enforces licencing restrictions when no geocoding service is explicitly provided.
	 *
	 * @param string $service
	 * @param string $mappingService
	 * 
	 * @return string
	 */
	private static function getValidGeoService( $service, $mappingService ) {
		global $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsGeoOverrides, $egMapsUserGeoOverrides;
		
		if ( $service == '' ) {
			if ( $egMapsUserGeoOverrides && $mappingService ) {
				// If no service has been provided, check if there are overrides for the default.
				foreach ( $egMapsAvailableGeoServices as $geoService ) {
					if ( array_key_exists( $geoService, $egMapsGeoOverrides ) && in_array( $mappingService, $egMapsGeoOverrides[$geoService] ) )  {
						$service = $geoService; // Use the override.
						break;
					}
				}				
			}

			// If no overrides where applied, use the default mapping service.
			if ( strlen( $service ) < 1 ) $service = $egMapsDefaultGeoService;
		}
		else {
			// If a service is provided, but is not supported, use the default.
			if ( ! array_key_exists( $service, $egMapsAvailableGeoServices ) ) $service = $egMapsDefaultGeoService;
		}

		return $service;
	}
}



