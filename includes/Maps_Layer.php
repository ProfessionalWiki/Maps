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
	 * List of layer type definitions.
	 * 
	 * @since 0.7.1
	 * 
	 * @var array
	 */
	protected static $types = array(
		'image' => array(
			'class' => 'OpenLayers.Layer.Image',
			'required' => array(
				'label',
				'source',
				'lowerbound',
				'upperbound',
				'leftbound',
				'rightbound',
				'width',
				'height'	
			),
			'optional' => array(
				'zoomlevels'
			)
		)
	);
	
	/**
	 * @since 0.7.1
	 * 
	 * @var array
	 */
	protected $properties;
	
	/**
	 * Returns the default layer type.
	 * 
	 * @since 0.7.1
	 * 
	 * @return string
	 */
	public static function getDefaultType() {
		return 'image';
	}
	
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
	
	/**
	 * Returns the type of the layer.
	 * 
	 * @since 0.7.1 
	 * 
	 * @param string
	 */	
	public function getType() {
		return array_key_exists( 'type', $this->properties ) ? $this->properties['type'] : self::getDefaultType();
	}
	
	/**
	 * Gets if the properties make up a valid layer definition.
	 * 
	 * @since 0.7.1
	 * 
	 * @return boolean
	 */
	public function isValid() {
		if ( array_key_exists( $this->getType(), self::$types ) ) {
			$typeDefinition = self::$types[$this->getType()];
			
			// Loop over the required parameters.
			foreach ( $typeDefinition['required'] as $paramName ) {
				if ( !array_key_exists( $paramName, $this->properties ) ) {
					return false;
				}
			}
			
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Returns a string containing the JavaScript definition of this layer.
	 * Only call this function when you are sure the layer is valid!
	 * 
	 * @since 0.7.1
	 * 
	 * @return string
	 */
	public function getJavaScriptDefinition() {
		// Note: this is currently hardcoded for layers of type image.
		$label = Xml::encodeJsVar( $this->properties['label'] );
		$source = Xml::encodeJsVar( MapsMapper::getImageUrl( $this->properties['source'] ) );
		$lowerBound = Xml::encodeJsVar( (int)$this->properties['lowerbound'] );
		$upperBound = Xml::encodeJsVar( (int)$this->properties['upperbound'] );
		$leftBound = Xml::encodeJsVar( (int)$this->properties['leftbound'] );
		$rightBound = Xml::encodeJsVar( (int)$this->properties['rightbound'] );	
		$width = Xml::encodeJsVar( (int)$this->properties['width'] );	
		$height = Xml::encodeJsVar( (int)$this->properties['height'] );			
		
		$class = self::$types[$this->getType()]['class'];
		
		$options = array();
		
		if ( array_key_exists( 'zoomlevels', $this->properties ) ) {
			$options['numZoomLevels'] = (int)$this->properties['zoomlevels'];
		}
		
		$options = Xml::encodeJsVar( (object)$options );
		
		return <<<EOT
	new $class(
		$label,
		$source,
		new OpenLayers.Bounds($leftBound, $lowerBound, $rightBound, $upperBound),
		new OpenLayers.Size($width, $height),
		{$options}
	)
EOT;
	}
	
}
