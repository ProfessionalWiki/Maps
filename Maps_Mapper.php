<?php

/** 
 * A class that holds static helper functions for common functionality that is not map-spesific.
 *
 * @file Maps_Mapper.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
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

		self::$mainParams = array
			(
			'zoom' => array(
				'type' => 'integer',
				'criteria' => array(
					'not_empty' => array()
					)				
				),
			'width' => array(
				'type' => 'integer',
				'criteria' => array(
					'not_empty' => array(),
					'in_range' => $egMapsSizeRestrictions['width']
					),
				'default' => $egMapsMapWidth		
				),
			'height' => array(
				'type' => 'integer',
				'criteria' => array(
					'not_empty' => array(),
					'in_range' => $egMapsSizeRestrictions['height']
					),
				'default' => $egMapsMapHeight
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
	public static function getValidService($service, $feature, $subfeature = '') {
		global $egMapsAvailableServices, $egMapsDefaultService, $egMapsDefaultServices, $egMapsServices;
		
		$service = self::getMainServiceName($service);
		
		$shouldChange = ! array_key_exists($service, $egMapsServices);
		if (! $shouldChange) {
			if (array_key_exists($feature, $egMapsServices[$service])) {
				$shouldChange = is_array($egMapsServices[$service][$feature]) && !array_key_exists($subfeature, $egMapsServices[$service][$feature]);
			}
			else {
				$shouldChange = true;
			}
		}
		
		if ($shouldChange) {
			if (array_key_exists($feature, $egMapsDefaultServices)) {
				if (is_array($egMapsDefaultServices[$feature])) {
					if (array_key_exists($subfeature, $egMapsDefaultServices[$feature])) {
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
		
		if(! in_array($service, $egMapsAvailableServices)) $service = $egMapsDefaultService;
		
		return $service;
	}
	
	/**
	 * Checks if the service name is an alias for an actual service,
	 * and changes it into the main service name if this is the case.
	 *
	 * @param string $service
	 * @return string
	 */
	private static function getMainServiceName($service) {
		global $egMapsServices;
		
		if (! array_key_exists($service, $egMapsServices)) {
			foreach ($egMapsServices as $serviceName => $serviceInfo) {
				if (in_array($service, $serviceInfo['aliases'])) {
					 $service = $serviceName;
					 break;
				}
			}
		}
		
		return $service;
	}	
}
