<?php

/**
 * Class for geocoder functionality of the Maps extension. 
 * 
 * @since 0.4
 * 
 * @file Maps_Geocoders.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
final class MapsGeocoders {
	
	/**
	 * The geocoder cache, holding geocoded data when enabled.
	 *
	 * @since 0.7
	 *
	 * @var array
	 */
	private static $mGeocoderCache = array();	
	
	/**
	 * Initialization function for Maps geocoder functionality.
	 * 
	 * @since 0.4
	 * 
	 * @return true
	 */
	public static function initialize() {
		global $wgAutoloadClasses, $egMapsDir, $egMapsGeoServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsGeoOverrides;
		
		$egMapsGeoServices = array();
		$egMapsGeoOverrides = array();
		
		$geoDir = dirname( __FILE__ ) . '/Geocoders/';
		// TODO: replace by autoloading
		include_once $geoDir . 'Maps_GoogleGeocoder.php'; 		// Google
		include_once $geoDir . 'Maps_YahooGeocoder.php'; 		// Yahoo!
		include_once $geoDir . 'Maps_GeonamesGeocoder.php'; 	// GeoNames

		// Remove the supported geocoding services that are not in the $egMapsAvailableGeoServices array.
		$supportedServices = array_keys( $egMapsGeoServices );
		foreach ( $supportedServices as $supportedService ) {
			if ( !in_array( $supportedService, $egMapsAvailableGeoServices ) ) {
				unset( $egMapsGeoServices[$supportedService] );
			}
		}
		
		// Re-populate the $egMapsAvailableGeoServices with it's original services that are supported. 
		$egMapsAvailableGeoServices = array_keys( $egMapsGeoServices );
		
		// Enure that the default geoservice is one of the enabled ones.
		if ( !in_array( $egMapsDefaultGeoService, $egMapsAvailableGeoServices ) ) {
			reset( $egMapsAvailableGeoServices );
			$egMapsDefaultGeoService = key( $egMapsAvailableGeoServices );
		}
		
		return true;
	}
	
	/**
	 * This function first determines wether the provided string is a pair or coordinates 
	 * or an address. If it's the later, an attempt to geocode will be made. The function will
	 * return the coordinates or false, in case a geocoding attempt was made but failed. 
	 * 
	 * @since 0.7
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
	 * 
	 * @since 0.7
	 * 
	 * @param string $coordsOrAddress
	 * 
	 * @return boolean
	 */
	public static function isLocation( $coordsOrAddress, $geoService = '', $mappingService = false ) {
		return self::attemptToGeocode( $coordsOrAddress, $geoService, $mappingService ) !== false;
	}
	
	/**
	 * Geocodes an address with the provided geocoding service and returns the result 
	 * as a string with the optionally provided format, or false when the geocoding failed.
	 * 
	 * @since 0.7
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
	 * @since 0.7
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
		if ( $egMapsEnableGeoCache && array_key_exists( $address, self::$mGeocoderCache ) ) {
			return self::$mGeocoderCache[$address];
		}

		$service = self::getValidGeoService( $service, $mappingService );
		
		// Call the geocode function in the specific geocoder class.
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
			self::$mGeocoderCache[$address] = $coordinates;
		}

		return $coordinates;
	}
	
	/**
	 * Does the same as Geocode, but also formats the result into a string.
	 * 
	 * @since 0.7
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
	 * @since 0.7
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