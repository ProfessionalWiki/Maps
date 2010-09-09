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
	
	/**
	 * In some usecases, the parameter values will contain extra location
	 * metadata, which should be ignored here. This field holds the delimiter
	 * used to seperata this data from the actual location. 
	 * 
	 * @since 0.7
	 * 
	 * @var string
	 */
	protected $metaDataSeparator;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7
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