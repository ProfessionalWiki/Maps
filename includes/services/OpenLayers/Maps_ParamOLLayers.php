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
		global $egMapsOLLayerGroups, $egMapsOLAvailableLayers;
		
		$unpacked = array();
		$imageLayers = array();
		
		foreach ( $parameter->getValue() as $layerOrGroup ) {
			if ( array_key_exists( $layerOrGroup, $egMapsOLLayerGroups ) ) {
				$unpacked += $egMapsOLLayerGroups[$layerOrGroup];
			}
			elseif ( in_array( $layerOrGroup, $egMapsOLAvailableLayers ) ) {
				$unpacked[] = $layerOrGroup;
			}
			else {
				$title = Title::newFromText( $layerOrGroup, Maps_NS_LAYER );
				
				if ( $title->getNamespace() == Maps_NS_LAYER && $title->exists() ) {
					$layerPage = new MapsLayerPage( $title );
					$imageLayers[] = $layerPage->getLayer();
				}
				else {
					wfWarn( "Invalid layer ($layerOrGroup) encountered after validation." );
				}
			}
		}
		
		$parameter->setValue( array( $unpacked, $imageLayers ) );	
	}
	
}