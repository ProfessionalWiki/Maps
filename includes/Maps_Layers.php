<?php

/**
 * Static class for layer functionality.
 *
 * @since 0.7.2
 * 
 * @file Maps_Layers.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsLayers {
	
	protected static $classes = array();
	protected static $services = array();
	
	/**
	 * Returns a new instance of a layer class for the provided layer type.
	 * 
	 * @since 0.7.2
	 * 
	 * @param $string $type
	 * 
	 * @return MapsLayer
	 */
	public static function getLayer( $type ) {
		if ( self::hasLayer( $type ) ) {
			return new self::$classes[$type]();
		}
		else {
			throw new exception( "There is no layer class for layer of type $type." );
		}
	}

	/**
	 * Returns if there is a layer class for the provided layer type.
	 * 
	 * @since 0.7.2
	 * 
	 * @param $string $type
	 * 
	 * @return boolean
	 */	
	public static function hasLayer( $type, $service = null ) {
		if ( array_key_exists( $type, self::$classes ) && array_key_exists( $type, self::$services ) ) {
			return is_null( $service ) || in_array( $service, self::$services[$type] );
		}
		else {
			return false;
		}
	}
	
	/**
	 * Register a layer.
	 * 
	 * @since 0.7.2
	 */
	public static function registerLayer( $type, $layerClass, $serviceIdentifier ) {
		self::$classes[$type] = $layerClass;
		self::$services[$type][] = $serviceIdentifier;
	}
	
	/**
	 * Initializes the layers functionality by registering the layer types
	 * by firing the  hook.
	 * 
	 * @since 0.7.2
	 */
	protected static function initializeLayers() {
		wfRunHooks( 'MappingLayersInitialization' );
	}
	
}