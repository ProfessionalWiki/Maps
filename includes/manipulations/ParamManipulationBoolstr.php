<?php

/**
 * Parameter manipulation converting the value to a boolean value as string.
 * 
 * @since 0.7.6
 * 
 * @file ParamManipulationBoolstr.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 * 
 * @author Jeroen De Dauw
 */
class ParamManipulationBoolstr extends ItemParameterManipulation {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7.6
	 */
	public function __construct() {
		parent::__construct();
	}	
	
	/**
	 * @see ItemParameterManipulation::doManipulation
	 * 
	 * @since 0.7.6
	 */	
	public function doManipulation( &$value, Parameter $parameter, array &$parameters ) {
		$value = $value ? 'true' : 'false';
	}
	
}