<?php
/**
 * A class that holds static helper functions for common functionality that is not map-spesific.
 *
 * @file Maps_Mapper.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsMapper {
	public static function initialize() {
		global $egMapsSizeRestrictions, $egMapsMapWidth, $egMapsMapHeight;

		Validator::addValidationFunction( 'is_map_dimension', array( __CLASS__, 'isMapDimension' ) );
		Validator::addValidationFunction( 'is_location', array( __CLASS__, 'isLocation' ) );
		Validator::addValidationFunction( 'are_locations', array( __CLASS__, 'areLocations' ) );

		Validator::addOutputFormat( 'mapdimension', array( __CLASS__, 'setMapDimension' ) );
		Validator::addOutputFormat( 'coordinateSet', array( __CLASS__, 'formatLocation' ) );
		Validator::addOutputFormat( 'coordinateSets', array( __CLASS__, 'formatLocations' ) );
	}

	public static function isLocation( $location, array $metaData ) {
		if ( self::geocoderIsAvailable() ) {
			return MapsGeocoder::isLocation( $location );
		} else {
			return MapsCoordinateParser::areCoordinates( $location );
		}
	}

	public static function areLocations( $locations, array $metaData ) {
		$locations = (array)$locations;
		foreach ( $locations as $location ) {
			if ( !self::isLocation( $location, $metaData ) ) {
				return false;
			}
		}
		return true;
	}

	public static function formatLocation( &$location, $name, array $parameters ) {
		if ( self::geocoderIsAvailable() ) {
			$location = MapsGeocoder::attemptToGeocodeToString( $location );
		} else {
			$location = MapsCoordinateParser::parseAndFormat( $location );
		}
	}

	public static function formatLocations( &$locations, $name, array $parameters ) {
		$locations = (array)$locations;
		foreach ( $locations as &$location ) {
			self::formatLocation( $location, $name, $parameters );
		}
	}

	/**
	 * Returns a valid service. When an invalid service is provided, the default one will be returned.
	 * Aliases are also chancged into the main service names @see MapsMapper::getMainServiceName().
	 *
	 * @param string $service
	 * @param string $feature
	 *
	 * @return string
	 */
	public static function getValidService( $service, $feature ) {
		global $egMapsServices, $egMapsDefaultService, $egMapsDefaultServices, $shouldChange;

		// Get rid of any aliases.
		$service = self::getMainServiceName( $service );
		// If the service is not loaded into maps, it should be changed.
		$shouldChange = ! array_key_exists( $service, $egMapsServices );

		// If it should not be changed, ensure the service supports this feature.
		if ( ! $shouldChange ) {
			$shouldChange = !array_key_exists( $feature, $egMapsServices[$service]['features'] );
		}

		// Change the service to the most specific default value available.
		// Note: the default services should support their corresponding features.
		// If they don't, a fatal error will occur later on.
		if ( $shouldChange ) {
			if ( array_key_exists( $feature, $egMapsDefaultServices ) ) {
				$service = $egMapsDefaultServices[$feature];
			}
			else {
				$service = $egMapsDefaultService;
			}
		}

		return $service;
	}

	/**
	 * Checks if the service name is an alias for an actual service,
	 * and changes it into the main service name if this is the case.
	 *
	 * @param string $service
	 * @return string
	 */
	private static function getMainServiceName( $service ) {
		global $egMapsServices;

		if ( ! array_key_exists( $service, $egMapsServices ) ) {
			foreach ( $egMapsServices as $serviceName => $serviceInfo ) {
				if ( in_array( $service, $serviceInfo['aliases'] ) ) {
					 $service = $serviceName;
					 break;
				}
			}
		}

		return $service;
	}

	/**
	 * Determines if a value is a valid map dimension, and optionally corrects it.
	 *
	 * @param string or number $value The value as it was entered by the user.
	 * @param string $dimension Must be width or height.
	 * @param boolean $correct If true, the value will be corrected when invalid. Defaults to false.
	 * @param number $default The default value for this dimension. Must be set when $correct = true.
	 *
	 * @return boolean
	 */
	public static function isMapDimension( &$value, array $metaData, $dimension, $correct = false, $default = 0 ) {
		global $egMapsSizeRestrictions;

		// See if the notation is valid.
		if ( !preg_match( '/^\d+(\.\d+)?(px|ex|em|%)?$/', $value ) ) {
			if ( $correct ) {
				$value = $default;
			} else {
				return false;
			}
		}

		// Determine the minimum and maximum values.
		if ( preg_match( '/^.*%$/', $value ) ) {
			if ( count( $egMapsSizeRestrictions[$dimension] >= 4 ) ) {
				$min = $egMapsSizeRestrictions[$dimension][2];
				$max = $egMapsSizeRestrictions[$dimension][3];
			} else {
				// This is for backward compatibility with people who have set a custom min and max before 0.6.
				$min = 1;
				$max = 100;
			}
		} else {
			$min = $egMapsSizeRestrictions[$dimension][0];
			$max = $egMapsSizeRestrictions[$dimension][1];
		}

		// See if the actual value is withing the limits.
		$number = preg_replace( '/[^0-9]/', '', $value );
		if ( $number < $egMapsSizeRestrictions[$dimension][0] ) {
			if ( $correct ) {
				$value = $egMapsSizeRestrictions[$dimension][0];
			} else {
				return false;
			}
		} else if ( $number > $egMapsSizeRestrictions[$dimension][1] ) {
			if ( $correct ) {
				$value = $egMapsSizeRestrictions[$dimension][1];
			} else {
				return false;
			}
		}

		// If this is a 'correct the value call', add 'px' if no unit has been provided.
		if ( $correct ) {
			if ( !preg_match( '/(px|ex|em|%)$/', $value ) ) {
				$value .= 'px';
			}
		}

		return true;
	}

	/**
	 * Corrects the provided map demension value when not valid.
	 *
	 * @param string or number $value The value as it was entered by the user.
	 * @param string $dimension Must be width or height.
	 * @param number $default The default value for this dimension.
	 */
	public static function setMapDimension( &$value, $name, array $parameters, $dimension, $default ) {
		self::isMapDimension( $value, array(), $dimension, true, $default );
	}

	/**
	 * Returns a boolean indicating if MapsGeocoder is available.
	 *
	 * @return Boolean
	 */
	public static function geocoderIsAvailable() {
		global $wgAutoloadClasses;
		return array_key_exists( 'MapsGeocoder', $wgAutoloadClasses );
	}

	/**
	 * Returns an array containing all the possible values for the service parameter, including aliases.
	 *
	 * @return array
	 */
	public static function getAllServiceValues() {
		global $egMapsAvailableServices, $egMapsServices;

		$allServiceValues = array();

		foreach ( $egMapsAvailableServices as $availableService ) {
			$allServiceValues[] = $availableService;
			$allServiceValues = array_merge( $allServiceValues, $egMapsServices[$availableService]['aliases'] );
		}

		return $allServiceValues;
	}

	/**
	 * This function returns the definitions for the parameters used by every map feature.
	 *
	 * @return array
	 */
	public static function getCommonParameters() {
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsMapWidth, $egMapsMapHeight;

		return array(
			'service' => array(
				'criteria' => array(
					'in_array' => self::getAllServiceValues()
				),
			),
			'geoservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService,
				'dependencies' => array( 'service' )
			),
			'zoom' => array(
				'type' => 'integer',
				'criteria' => array(
					'not_empty' => array()
				),
				'default' => 'null'
			),
			'width' => array(
				'criteria' => array(
					'is_map_dimension' => array( 'width' ),
				),
				'default' => $egMapsMapWidth,
				'output-type' => array( 'mapdimension', 'width', $egMapsMapWidth )
			),
			'height' => array(
				'criteria' => array(
					'is_map_dimension' => array( 'height' ),
				),
				'default' => $egMapsMapHeight,
				'output-type' => array( 'mapdimension', 'height', $egMapsMapHeight )
			),
		);
	}
}
