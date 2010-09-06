<?php

/**
 * Parameter criterion stating that the value must be a set of coordinates or an address.
 * 
 * @since 0.7
 * 
 * @file CriterionAreLocations.php
 * @ingroup Maps
 * @ingroup Criteria
 * 
 * @author Jeroen De Dauw
 */
class CriterionAreLocations extends ParameterCriterion {
	
	protected $metaDataSeparator;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.4
	 */
	public function __construct( $metaDataSeparator = false ) {
		parent::__construct();
		
		$this->metaDataSeparator = $metaDataSeparator;
	}
	
	/**
	 * @see ParameterCriterion::validate
	 */	
	public function validate( $value ) {
		if ( $this->metaDataSeparator !== false ) {
			$parts = explode( $this->metaDataSeparator, $value );
			$value = $parts[0];
		}
		
		if ( MapsGeocoders::canGeocode() ) {
			// TODO
			//$geoService = array_key_exists( 'geoservice', $parameters ) ? $parameters['geoservice']['value'] : '';
			//$mappingService = array_key_exists( 'mappingservice', $parameters ) ? $parameters['mappingservice']['value'] : false;
			
			return MapsGeocoders::isLocation( $value/*, $geoService, $mappingService */ );
		} else {
			return MapsCoordinateParser::areCoordinates( $value );
		}
	}
	
}