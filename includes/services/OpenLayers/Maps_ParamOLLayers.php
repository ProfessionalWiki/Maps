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
		
		$layerDefs = array();
		$layerNames = array();
		
		foreach ( $parameter->getValue() as $layerOrGroup ) {
			// Layer groups. Loop over all items and add them when not present yet.
			if ( array_key_exists( $layerOrGroup, $egMapsOLLayerGroups ) ) {
				foreach ( $egMapsOLLayerGroups[$layerOrGroup] as $layerName ) {
					if ( !in_array( $layerName, $layerNames ) ) {
						if ( is_array( $egMapsOLAvailableLayers[$layerName] ) ) {
							$layerDefs[] = 'new ' . $egMapsOLAvailableLayers[$layerName][0];
						}
						else {
							$layerDefs[] = 'new ' . $egMapsOLAvailableLayers[$layerName];
						}
						$layerNames[] = $layerName;
					}
				}
			}
			// Single layers. Add them when not present yet.
			elseif ( array_key_exists( $layerOrGroup, $egMapsOLAvailableLayers ) ) {
				if ( !in_array( $layerOrGroup, $layerNames ) ) {
					if ( is_array( $egMapsOLAvailableLayers[$layerOrGroup] ) ) {
						$layerDefs[] = 'new ' . $egMapsOLAvailableLayers[$layerOrGroup][0];
					}
					else {
						$layerDefs[] = 'new ' . $egMapsOLAvailableLayers[$layerOrGroup];
					}
					
					$layerNames[] = $layerOrGroup;
				}
			}
			// Image layers. Check validity and add when not present yet.
			else {
				$title = Title::newFromText( $layerOrGroup, Maps_NS_LAYER );
				
				if ( $title->getNamespace() == Maps_NS_LAYER && $title->exists() ) {
					$layerPage = new MapsLayerPage( $title );
					$layer = $layerPage->getLayer();
					
					if ( $layer->isValid() ) {
						if ( !in_array( $layerOrGroup, $layerNames ) ) {
							$layerDefs[] = $layer->getJavaScriptDefinition();
							$layerNames[] = $layerOrGroup;							
						}
					}
					else {
						wfWarn( "Invalid layer ($layerOrGroup) encountered after validation." );
					}
				}
				else {
					wfWarn( "Invalid layer ($layerOrGroup) encountered after validation." );
				}
			}
		}

		$parameter->setValue( array(
			'[' . implode( ',', $layerDefs ) . ']',
			$this->getDependencies( $layerNames )
		) );
	}
	
	/**
	 * Returns the depencies for the provided layers.
	 * 
	 * @since 0.7.1
	 * 
	 * @param array $layerNames
	 * 
	 * @return array
	 */
	protected function getDependencies( array $layerNames ) {
		global $egMapsOLLayerDependencies, $egMapsOLAvailableLayers;
		
		$layerDependencies = array();
		
		foreach ( $layerNames as $layerName ) {
			if ( is_array( $egMapsOLAvailableLayers[$layerName] ) 
				&& count( $egMapsOLAvailableLayers[$layerName] ) > 1
				&& array_key_exists( $egMapsOLAvailableLayers[$layerName][1], $egMapsOLLayerDependencies ) ) {
				$layerDependencies[] = $egMapsOLLayerDependencies[$egMapsOLAvailableLayers[$layerName][1]];
			}
		}

		return array_unique( $layerDependencies );
	}
	
}