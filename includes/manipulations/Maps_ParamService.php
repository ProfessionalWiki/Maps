<?php

/**
 * Parameter manipulation ensuring the service is valid and loading adittional parameter definitions.
 * 
 * @since 0.7
 * 
 * @file Maps_ParamService.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 * 
 * @author Jeroen De Dauw
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
	 * Adittional parameter definitions to load.
	 * 
	 * @since 0.7
	 * 
	 * @var array of Parameter
	 */
	protected $serviceParams;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7
	 */
	public function __construct( $feature, array $serviceParams = array() ) {
		parent::__construct();
		
		$this->feature = $feature;
		$this->serviceParams = $serviceParams;
	}
	
	/**
	 * @see ItemParameterManipulation::doManipulation
	 * 
	 * @since 0.7
	 */	
	public function doManipulation( &$value, array &$parameters ) {
		// Make sure the service is valid.
		$value = MapsMappingService::getValidServiceName( $value, $this->feature );
		
		// Add the service specific service parameters.
		$parameters = array_merge( $parameters, $this->serviceParams );
	}
	
}