<?php

/**
 * File holding class MapsMapFeature.
 *
 * @file Maps_MapFeature.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * MapsMapFeature bundles base functionallity for all general mapping feature classes.
 * 
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 * 
 * TODO: refactor this and subclasses to follow mw conventions and simply have a better design pattern.
 */
abstract class MapsMapFeature {
	
	/**
	 * Set the map service specific element name and the javascript function handling the displaying of an address
	 */
	protected abstract function setMapSettings();
	
	/**
	 * Map service specific map count and loading of dependencies
	 */
	protected abstract function doMapServiceLoad();
	
	/**
	 * Adds the HTML specific to the mapping service to the output
	 */
	protected abstract function addSpecificMapHTML( Parser $parser );
	
	public $serviceName;
	
	protected $elementNr;
	protected $elementNamePrefix;
	
	protected $mapName;
	
	protected $centreLat, $centreLon;

	protected $output = '';

	protected $spesificParameters = false;
	protected $featureParameters = false;
	
	/**
	 * Sets the map properties as class fields.
	 * 
	 * @param array $mapProperties
	 * @param string $className
	 */
	protected function setMapProperties( array $mapProperties, $className ) {
		foreach ( $mapProperties as $paramName => $paramValue ) {
			if ( !property_exists( $className, $paramName ) ) {
				$this-> { $paramName } = $paramValue;
			}
			else {
				// If this happens in any way, it could be a big vunerability, so throw an exception.
				throw new Exception( 'Attempt to override a class field during map property assignment. Field name: ' . $paramName );
			}
		}
	}
	
	/**
	 * Sets the $mapName field, using the $elementNamePrefix and $elementNr.
	 */
	protected function setMapName() {
		$this->mapName = $this->elementNamePrefix . '_' . $this->elementNr;
	}
	
	/**
	 * @return array
	 */
	public function getSpecificParameterInfo() {
		return array();
	}

	/**
	 * @return array
	 */	
	public function getFeatureParameters() {
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsMapWidth, $egMapsMapHeight;
		
		return array(	
			'service' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableServices
				),		
			),
			'geoservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
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