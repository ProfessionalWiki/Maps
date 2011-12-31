<?php

/**
 * Parameter manipulation ensuring the service is valid and loading additional parameter definitions.
 * 
 * @since 0.7
 * 
 * @file Maps_ParamService.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 *
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsParamService extends ItemParameterManipulation {
	
	/**
	 * The mapping feature. Needed to determine which services are available.
	 * 
	 * @since 0.7
	 * 
	 * @var string
	 */
	protected $feature;
	
	/**
	 * Constructor.
	 *
	 * @param string $feature
	 *
	 * @since 0.7
	 */
	public function __construct( $feature ) {
		parent::__construct();
		
		$this->feature = $feature;
	}
	
	/**
	 * @see ItemParameterManipulation::doManipulation
	 * 
	 * @since 0.7
	 */	
	public function doManipulation( &$value, Parameter $parameter, array &$parameters ) {
		// Make sure the service is valid.
		$value = MapsMappingServices::getValidServiceName( $value, $this->feature );
		
		// Get the service object so the service specific parameters can be retrieved.
		$serviceObject = MapsMappingServices::getServiceInstance( $value );
		
		// Add the service specific service parameters.
		$serviceObject->addParameterInfo( $parameters );
	}
	
}