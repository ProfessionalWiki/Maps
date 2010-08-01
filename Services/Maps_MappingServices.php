<?php

/**
 * File holding the MapsMappingServices class.
 *
 * @file Maps_MappingServices.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class serving as a factory for the MapsMappingService classes.
 * 
 * @since 0.6.6
 * 
 * @author Jeroen De Dauw
 */
class MapsMappingServices {
	
	protected static $services = array();
	
	public static function registerService() {
		
	}
	
	/**
	 * Retuns an instance of a MapsMappingService. The service name is validated
	 * and aliases are resolved and a check is made if the feature is supported.
	 * If the feature is not supported, or the service does not exist, defaulting
	 * will be used.
	 * 
	 * @since 0.6.6
	 * 
	 * @param string $service
	 * @param string $feature
	 * 
	 * @return iMappingService
	 */
	public static function getServiceInstance( $service, $feature ) {
		global $egMapsServices;
		return $egMapsServices[self::getValidService( $service, $feature )];
	}
	
	/**
	 * Returns a valid service. When an invalid service is provided, the default one will be returned.
	 * Aliases are also chancged into the main service names @see MapsMapper::getMainServiceName.
	 *
	 * @since 0.6.6
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
		$shouldChange = !array_key_exists( $service, $egMapsServices );

		// If it should not be changed, ensure the service supports this feature.
		if ( !$shouldChange ) {
			$shouldChange = $egMapsServices[$service]->getFeature( $feature ) === false;
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
	 * @since 0.6.6
	 *
	 * @param string $service
	 * 
	 * @return string
	 */
	protected static function getMainServiceName( $service ) {
		global $egMapsServices;

		if ( !array_key_exists( $service, $egMapsServices ) ) {
			foreach ( $egMapsServices as $serviceObject ) {
				if ( $serviceObject->hasAlias( $service ) ) {
					 $service = $serviceObject->getName();
					 break;
				}
			}
		}

		return $service;
	}		
	
}