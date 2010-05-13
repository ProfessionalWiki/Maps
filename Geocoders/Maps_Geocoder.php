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
	 * @param string $mappingService
	 * @param boolean $checkForCoords
	 * @param boolean $checkForCoords
	 * 
	 * @return array or false
	 */
	public static function attemptToGeocode( $coordsOrAddress, $geoservice = '', $mappingService = false, $checkForCoords = true ) {
		if ( $checkForCoords ) {
			if ( MapsCoordinateParser::areCoordinates( $coordsOrAddress ) ) {
				return MapsCoordinateParser::parseCoordinates( $coordsOrAddress );
			} else {
				return self::geocode( $coordsOrAddress, $geoservice, $mappingService );
			}
		} else {
			return self::geocode( $coordsOrAddress, $geoservice, $mappingService );
		}
	}
	
	/**
	 * 
	 * @param string $coordsOrAddress
	 * 
	 * @return boolean
	 */
	public static function isLocation( $coordsOrAddress ) {
		return self::attemptToGeocode( $coordsOrAddress ) !== false;
	}
	
	/**
	 * Geocodes an address with the provided geocoding service and returns the result 
	 * as a string with the optionally provided format, or false when the geocoding failed.
	 * 
	 * @param string $coordsOrAddress
	 * @param string $service
	 * @param string $mappingService
	 * @param boolean $checkForCoords
	 * @param coordinate type $targetFormat The notation to which they should be formatted. Defaults to floats.
	 * @param boolean $directional Indicates if the target notation should be directional. Defaults to false.
	 * 
	 * @return formatted coordinates string or false
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
	public static function geocode( $address, $service = '', $mappingService = false ) {
		global $egMapsGeoServices, $wgAutoloadClasses, $egMapsDir, $IP, $egMapsEnableGeoCache;
		
		// If the adress is already in the cache and the cache is enabled, return the coordinates.
		if ( $egMapsEnableGeoCache && array_key_exists( $address, MapsGeocoder::$mGeocoderCache ) ) {
			return self::$mGeocoderCache[$address];
		}
		
		$service = self::getValidGeoService( $service, $mappingService );

		// Call the geocode function in the spesific geocoder class.
		$coordinates = call_user_func( array( $egMapsGeoServices[$service], 'geocode' ), $address );
		
		// If there address could not be geocoded, and contains comma's, try again without the comma's.
		// This is cause several geocoding services such as geonames do not handle comma's well.
		if ( !$coordinates && strpos( $address, ',' ) !== false ) {
			$coordinates = call_user_func(
				array( $egMapsGeoServices[$service], 'geocode' ), str_replace( ',', '', $address )
			);
		}
		
		// Add the obtained coordinates to the cache when there is a result and the cache is enabled.
		if ( $egMapsEnableGeoCache && $coordinates ) {
			MapsGeocoder::$mGeocoderCache[$address] = $coordinates;
		}

		return $coordinates;
	}
	
	/**
	 * Does the same as Geocode, but also formats the result into a string.
	 * 
	 * @param string $coordsOrAddress
	 * @param string $service
	 * @param string $mappingService
	 * @param coordinate type $targetFormat The notation to which they should be formatted. Defaults to floats.
	 * @param boolean $directional Indicates if the target notation should be directional. Defaults to false.
	 * 
	 * @return formatted coordinates string or false
	 */
	public static function geocodeToString( $address, $service = '', $mappingService = false, $targetFormat = Maps_COORDS_FLOAT, $directional = false  ) {
		$coordinates = self::geocode( $address, $service, $mappingService );
		return $coordinates ?  MapsCoordinateParser::formatCoordinates( $coordinates, $targetFormat, $directional ) : false;
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
			if ( $service == '' ) $service = $egMapsDefaultGeoService;
		}
		else {
			// If a service is provided, but is not supported, use the default.
			if ( !in_array( $service, $egMapsAvailableGeoServices ) ) $service = $egMapsDefaultGeoService;
		}

		return $service;
	}
}