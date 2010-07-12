<?php

/**
 * File holding class MapsBaseMap.
 *
 * @file Maps_BaseMap.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Abstract class MapsBaseMap provides the scafolding for classes handling display_map
 * calls for a specific mapping service. It inherits from MapsMapFeature and therefore
 * forces inheriting classes to implement sereveral methods.
 *
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */
abstract class MapsBaseMap implements iMapParserFunction {
	
	protected $service;
	
	protected $centreLat, $centreLon;

	protected $output = '';

	protected $parser;
	
	private $specificParameters = false;
	protected $featureParameters = false;
	
	/**
	 * Constructor.
	 * 
	 * @param MapsMappingService $service
	 */
	public function __construct( MapsMappingService $service ) {
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
	 * @return array
	 */
	public function getFeatureParameters() {
		global $egMapsDefaultServices, $egMapsMapWidth, $egMapsMapHeight;
		
		return array(
			'width' => array(
				'default' => $egMapsMapWidth
			),
			'height' => array(
				'default' => $egMapsMapHeight
			),			
			'mappingservice' => array(
				'default' => $egMapsDefaultServices['display_map']
			),
			'coordinates' => array(
				'required' => true,
				'aliases' => array( 'coords', 'location', 'address' ),
				'criteria' => array(
					'is_location' => array()
				),
				'output-type' => 'coordinateSet',
			),
		);
	}
	
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param Parser $parser
	 * @param array $params
	 * 
	 * @return html
	 */
	public final function getMapHtml( Parser &$parser, array $params ) {
		$this->parser = $parser;
		
		$this->featureParameters = MapsDisplayMap::$parameters;

		$this->setMapProperties( $params );
		
		$this->setCentre();
		
		if ( $this->zoom == 'null' ) {
			$this->zoom = $this->service->getDefaultZoom();
		}
		
		$this->addSpecificMapHTML();
		
		$this->service->addDependencies( $this->parser );
		
		return $this->output;
	}
	
	/**
	 * Sets the $centreLat and $centreLon fields.
	 */
	private function setCentre() {
		if ( empty( $this->coordinates ) ) { // If centre is not set, use the default latitude and longitutde.
			$this->setDefaultCentre();
		}
		else { // If a centre value is set, geocode when needed and use it.
			$this->coordinates = MapsGeocoder::attemptToGeocode( $this->coordinates, $this->geoservice, $this->service->getName() );

			// If the centre is not false, it will be a valid coordinate, which can be used to set the  latitude and longitutde.
			if ( $this->coordinates ) {
				$this->centreLat = Xml::escapeJsString( $this->coordinates['lat'] );
				$this->centreLon = Xml::escapeJsString( $this->coordinates['lon'] );
			}
			else { // If it's false, the coordinate was invalid, or geocoding failed. Either way, the default's should be used.
				// TODO: Some warning this failed would be nice here. 
				$this->setDefaultCentre();
			}
		}
	}
	
	/**
	 * Sets the centre lat and lon to their default.
	 */
	private function setDefaultCentre() {
		global $egMapsMapLat, $egMapsMapLon;
		
		$this->centreLat = $egMapsMapLat;
		$this->centreLon = $egMapsMapLon;		
	}
	
}