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
	
	/**
	 * Array holding the parameters that are not spesific to a mapping service, 
	 * their aliases, criteria and default value.
	 *
	 * @var array
	 */
	private static $mainParams;

	public static function initializeMainParams() {
		global $egMapsSizeRestrictions, $egMapsMapWidth, $egMapsMapHeight;

		Validator::addOutputFormat( 'mapdimension', array( __CLASS__, 'setMapDimension' ) );
		Validator::addValidationFunction( 'is_map_dimension', array( __CLASS__, 'isMapDimension' ) );			
		
		self::$mainParams = array (
			'zoom' => array(
				'type' => 'integer',
				'criteria' => array(
					'not_empty' => array()
				)
			),
			'width' => array(
				'criteria' => array(
					'is_map_dimension' => array( 'width' ),
				),
				'default' => $egMapsMapWidth,
				'output-type' => array( 'mapdimension', 'width', true, $egMapsMapWidth )
			),
			'height' => array(
				'criteria' => array(
					'is_map_dimension' => array( 'height' ),
				),
				'default' => $egMapsMapHeight,
				'output-type' => array( 'mapdimension', 'height', true, $egMapsMapHeight )
			),
		);
	}

	/**
	 * Returns the main parameters array.
	 * 
	 * @return array
	 */
	public static function getMainParams() {
		return self::$mainParams;
	}
	
	/**
	 * Returns a valid service. When an invalid service is provided, the default one will be returned.
	 * Aliases are also chancged into the main service names @see MapsMapper::getMainServiceName().
	 *
	 * @param string $service
	 * @param string $feature
	 * @param string $subfeature
	 * 
	 * @return string
	 */
	public static function getValidService( $service, $feature, $subfeature = '' ) {
		global $egMapsAvailableServices, $egMapsDefaultService, $egMapsDefaultServices, $egMapsServices;

		// Get rid of any aliases.
		$service = self::getMainServiceName( $service );
		
		// If the service is not loaded into maps, it should be changed.
		$shouldChange = ! array_key_exists( $service, $egMapsServices );

		// If it should not be changed, ensure the service supports this feature, and when present, sub feature.
		// TODO: recursive checking for sub features would definitly be cooler.
		if ( ! $shouldChange ) {
			if ( array_key_exists( $feature, $egMapsServices[$service] ) ) {
				if ( array_key_exists( 'class', $egMapsServices[$service][$feature] ) ) {
					// If the class key is set, the feature does not have sub features, so the service supports the feature.
					$shouldChange = false;
				}
				else
				{
					// The feature has sub features, so check if the current service has support for it.
					$shouldChange = !array_key_exists( $subfeature, $egMapsServices[$service][$feature] );
				}
			}
			else {
				// The service does not support this feature.
				$shouldChange = true;
			}
		}

		// Change the service to the most specific default value available.
		// Note: the default services should support their corresponding features.
		// If they don't, a fatal error will occur later on.
		if ( $shouldChange ) {
			if ( array_key_exists( $feature, $egMapsDefaultServices ) ) {
				if ( is_array( $egMapsDefaultServices[$feature] ) ) {
					if ( array_key_exists( $subfeature, $egMapsDefaultServices[$feature] ) ) {
						$service = $egMapsDefaultServices[$feature][$subfeature];
					}
					else {
						$service = $egMapsDefaultService;
					}
				}
				else {
					$service = $egMapsDefaultServices[$feature];
				}
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
	
	public static function isMapDimension( &$value, $dimension, $correct = false, $default = 0 ) {
		global $egMapsSizeRestrictions;
		
		if ( !preg_match( '/^\d+(\.\d+)?(px|ex|em|%)?$/', $value ) ) {
			if ( $correct ) {
				$value = $default;
			} else {
				return false;
			}
		}
		
		if ( !preg_match( '/^.*%$/', $value ) ) {
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
		}
		
		if ( $correct ) {
			if ( !preg_match( '/(px|ex|em|%)$/', $value ) ) {
				$value .= 'px';
			}			
		}
		
		return true;		
	}
	
	public static function setMapDimension( &$value, $dimension, $correct, $default ) {
		global $egMapsMapWidth;
		self::isMapDimension( $value, $dimension, $correct, $default );	
	}

}
