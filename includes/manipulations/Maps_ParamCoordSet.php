<?php

/**
 * Parameter manipulation ensuring the value is a coordinate set.
 * 
 * @since 0.7
 * 
 * @file Maps_ParamCoordSet.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 * 
 * @author Jeroen De Dauw
 */
class MapsParamCoordSet extends ItemParameterManipulation {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @see ItemParameterManipulation::doManipulation
	 * 
	 * @since 0.7
	 */	
	public function doManipulation( &$value, array &$parameters ) {
		if ( MapsGeocoders::canGeocode() ) {
			// TODO
			$value = MapsGeocoders::attemptToGeocodeToString( $value/*, $geoService, $mappingService*/ );
		} else {
			$value = MapsCoordinateParser::parseAndFormat( $value );
		}
	}
	
}