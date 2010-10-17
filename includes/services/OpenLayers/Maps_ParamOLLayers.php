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
			if ( array_key_exists( $layerOrGroup, $egMapsOLLayerGroups ) ) {
				$layerNames += $egMapsOLLayerGroups[$layerOrGroup];
				
				foreach ( $egMapsOLLayerGroups[$layerOrGroup] as $layerName ) {
					$layerDefs[] = 'new ' . $egMapsOLAvailableLayers[$layerName];
				}
			}
			elseif ( array_key_exists( $layerOrGroup, $egMapsOLAvailableLayers ) ) {
				$layerDefs[] = 'new ' . $egMapsOLAvailableLayers[$layerOrGroup];
				$layerNames[] = $layerOrGroup;
			}
			else {
				$title = Title::newFromText( $layerOrGroup, Maps_NS_LAYER );
				
				if ( $title->getNamespace() == Maps_NS_LAYER && $title->exists() ) {
					$layerPage = new MapsLayerPage( $title );
					$layer = $layerPage->getLayer();
					
					if ( $layer->isValid() ) {
						$layerDefs[] = $layer->getJavaScriptDefinition();
						$layerNames[] = $layerOrGroup;
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
		global $egMapsOLLayerDependencies;
		static $decompressed = false;
		
		if ( !$decompressed ) {
			$this->decompressLayerDependencies();
			$decompressed = true;
		}
		
		$layerDependencies = array();
		
		foreach ( $layerNames as $layerName ) {
			if ( array_key_exists( $layerName, $egMapsOLLayerDependencies ) ) {
				
			}
		}

		return $layerDependencies;
	}
	
	/**
	 * Resolves group dependencies to individual layer dependencies.
	 * 
	 * @since 0.7.1
	 */
	protected static function decompressLayerDependencies() {
		global $egMapsOLLayerDependencies, $egMapsOLLayerGroups;
		
		foreach ( $egMapsOLLayerGroups as $groupName => $groupItems ) {
			if ( array_key_exists( $groupName, $egMapsOLLayerDependencies ) ) {
				foreach ( $groupItems as $item ) {
					$egMapsOLLayerDependencies[$item] = $egMapsOLLayerDependencies[$groupName];
				}
				unset($egMapsOLLayerDependencies[$groupName]);
			}
		}
	}
	
}