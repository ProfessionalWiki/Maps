<?php

/**
 * Class for describing map layers.
 *
 * @since 0.7.1
 * 
 * @file Maps_Layer.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsLayer {
	
	/**
	 * @since 0.7.1
	 * 
	 * @var array
	 */
	protected $properties;
	
	/**
	 * Creates and returns a new instance of an MapsLayer, based on the provided array of key value pairs.
	 * 
	 * @since 0.7.1
	 * 
	 * @param array $properties
	 * 
	 * @return MapsLayer
	 */
	public static function newFromArray( array $properties ) {
		$layer = new MapsLayer();
		$layer->setProperties( $properties );
		return $layer;
	}
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7.1
	 */
	public function __construct() {
		
	}
	
	/**
	 * Sets the properties.
	 * 
	 * @since 0.7.1 
	 * 
	 * @param array $properties
	 */
	public function setProperties( array $properties ) {
		$this->properties = $properties;
	}
	
}