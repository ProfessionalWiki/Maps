<?php

/**
 * Abstract class MapsBaseMap provides the scafolding for classes handling display_map
 * calls for a specific mapping service. It inherits from MapsMapFeature and therefore
 * forces inheriting classes to implement sereveral methods.
 *
 * @file Maps_BaseMap.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */
abstract class MapsBaseMap {
	
	/**
	 * @var iMappingService
	 */	
	protected $service;
	
	protected $centreLat, $centreLon;

	protected $output = '';

	private $specificParameters = false;
	/**
	 * Constructor.
	 * 
	 * @param MapsMappingService $service
	 */
	public function __construct( iMappingService $service ) {
		$this->service = $service;
	}
	
	/**
	 * Sets the map properties as class fields.
	 * 
	 * @param array $mapProperties
	 */
	protected function setMapProperties( array $mapProperties ) {
		foreach ( $mapProperties as $paramName => $paramValue ) {
			if ( !property_exists( __CLASS__, $paramName ) ) {
				$this-> { $paramName } = $paramValue;
			}
			else {
				// If this happens in any way, it could be a big vunerability, so throw an exception.
				throw new Exception( 'Attempt to override a class field during map property assignment. Field name: ' . $paramName );
			}
		}
	}
	
	/**
	 * Returns the specific parameters by first checking if they have been initialized yet,
	 * doing to work if this is not the case, and then returning them.
	 * 
	 * @return array
	 */
	public final function getSpecificParameterInfo() {
		if ( $this->specificParameters === false ) {
			$this->specificParameters = array();
			$this->initSpecificParamInfo( $this->specificParameters );
		}
		
		return $this->specificParameters;
	}
	
	/**
	 * Initializes the specific parameters.
	 * 
	 * Override this method to set parameters specific to a feature service comibination in
	 * the inheriting class.
	 * 
	 * @param array $parameters
	 */
	protected function initSpecificParamInfo( array &$parameters ) {
	}
	
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param array $params
	 * @param Parser $parser
	 * 
	 * @return html
	 */
	public final function getMapHtml( array $params, Parser $parser ) {
		$this->setMapProperties( $params );
		
		$this->setCentre();
		
		if ( $this->zoom == 'null' ) {
			$this->zoom = $this->service->getDefaultZoom();
		}
		
		$this->addSpecificMapHTML( $parser );
		
		global $wgTitle;
		if ( $wgTitle->getNamespace() == NS_SPECIAL ) {
			global $wgOut;
			$this->service->addDependencies( $wgOut );
		}
		else {
			$this->service->addDependencies( $parser );			
		}
		
		return $this->output;
	}
	
	/**
	 * Sets the $centreLat and $centreLon fields.
	 */
	protected function setCentre() {
		if ( empty( $this->coordinates ) ) { // If centre is not set, use the default latitude and longitutde.
			$this->setCentreToDefault();
		}
		else { // If a centre value is set, geocode when needed and use it.
			$this->coordinates = MapsGeocoders::attemptToGeocode( $this->coordinates, $this->geoservice, $this->service->getName() );

			// If the centre is not false, it will be a valid coordinate, which can be used to set the  latitude and longitutde.
			if ( $this->coordinates ) {
				$this->centreLat = Xml::escapeJsString( $this->coordinates['lat'] );
				$this->centreLon = Xml::escapeJsString( $this->coordinates['lon'] );
			}
			else { // If it's false, the coordinate was invalid, or geocoding failed. Either way, the default's should be used.
				$this->setCentreToDefault();
			}
		}
	}
	
	/**
	 * Attempts to set the centreLat and centreLon fields to the Maps default.
	 * When this fails (aka the default is not valid), an exception is thrown.
	 * 
	 * @since 0.7
	 */
	protected function setCentreToDefault() {
		global $egMapsDefaultMapCentre;
		
		$centre = MapsGeocoders::attemptToGeocode( $egMapsDefaultMapCentre, $this->geoservice, $this->service->getName() );
		
		if ( $centre === false ) {
			throw new Exception( 'Failed to parse the default centre for the map. Please check the value of $egMapsDefaultMapCentre.' );
		}
		else {
			$this->centreLat = Xml::escapeJsString( $centre['lat'] );
			$this->centreLon = Xml::escapeJsString( $centre['lon'] );			
		}
	}	
	
}