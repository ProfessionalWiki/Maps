<?php

/**
 * File holding class MapsMapFeature.
 *
 * @file Maps_MapFeature.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * MapsMapFeature bundles base functionallity for all general mapping feature classes.
 *
 * @author Jeroen De Dauw
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
	protected abstract function addSpecificMapHTML();
	
	public $serviceName;
	
	protected $defaultParams = array();		

	protected $defaultZoom;
	
	protected $elementNr;
	protected $elementNamePrefix;
	
	protected $mapName;
	
	protected $centre_lat;
	protected $centre_lon;

	protected $output = '';	
	protected $errors = array();
	
	/**
	 * Validates and corrects the provided map properties, and the sets them as class fields.
	 * 
	 * @param array $mapProperties
	 * @param string $className 
	 */
	protected function manageMapProperties(array $mapProperties, $className) {
		global $egMapsServices;
		
		// TODO: implement strict parameter validation, put errors in  array.
		$mapProperties = MapsMapper::getValidParams($mapProperties, $egMapsServices[$this->serviceName]['parameters']);
		$mapProperties = MapsMapper::setDefaultParValues($mapProperties, $this->defaultParams);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item if they don't exist on class level yet.
		foreach($mapProperties as $paramName => $paramValue) {
			if (! property_exists($className, $paramName)) {
				$this->{$paramName} = $paramValue;
			}
		}
		
		MapsMapper::enforceArrayValues($this->controls);
		
		MapsUtils::makeMapSizeValid($this->width, $this->height);
	}	
	
	/**
	 * Sets the $mapName field, using the $elementNamePrefix and $elementNr.
	 */
	protected function setMapName() {
		$this->mapName = $this->elementNamePrefix . '_' . $this->elementNr;
	}
	
}