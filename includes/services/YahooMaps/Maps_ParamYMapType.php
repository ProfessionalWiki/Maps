<?php

/**
 * Parameter manipulation ensuring the value is a Yahoo Maps! map type.
 * 
 * @since 0.7
 * 
 * @file Maps_ParamYMapType.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 * @ingroup MapsYahooMaps
 *
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsParamYMapType extends ItemParameterManipulation {
	
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
	public function doManipulation( &$value, Parameter $parameter, array &$parameters ) {
		$value = MapsYahooMaps::$mapTypes[strtolower( $value )];
	}
	
}