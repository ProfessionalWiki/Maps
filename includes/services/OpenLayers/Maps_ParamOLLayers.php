<?php

/**
 * Parameter manipulation ensuring the value is 
 * 
 * @since 0.7
 * 
 * @file Maps_ParamOLLayers.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 * @ingroup MapsOpenLayers
 * 
 * @author Jeroen De Dauw
 */
class MapsParamOLLayers extends ListParameterManipulation {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @see ParameterManipulation::manipulate
	 * 
	 * @since 0.7
	 */	
	public function manipulate( Parameter &$parameter, array &$parameters ) {
		global $egMapsOLLayerGroups;
		
		$unpacked = array();

		foreach ( $parameter->getValue() as $layerOrGroup ) {
			if ( array_key_exists( $layerOrGroup, $egMapsOLLayerGroups ) ) {
				$unpacked = array_merge( $unpacked, $egMapsOLLayerGroups[$layerOrGroup] );
			}
			else {
				$unpacked[] = $layerOrGroup;
			}
		}
		
		$parameter->setValue( $unpacked );		
	}
	
}