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
	 * @since 0.7.1
	 * 
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * Keeps track if the layer has been validated, to prevent doing redundant work.
	 * 
	 * @since 0.7.1
	 * 
	 * @var boolean
	 */
	protected $hasValidated = false;
	
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
	 * Constructor.
	 * 
	 * @since 0.7.1
	 * 
	 * @param array $properties
	 */
	public function __construct( array $properties ) {
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
	 * Returns the error messages, optionaly filtered by an error tag.
	 * 
	 * @since 0.7.1
	 * 
	 * @param mixed $tag
	 * 
	 * @return array of string
	 */
	public function getErrorMessages( $tag = false ) {
		$messages = array();
		
		foreach ( $this->errors as $error ) {
			if ( $tag === false || $error->hasTag( $tag ) ) {
				$messages[] = $error->getMessage();
			}
		}
		
		return $messages;
	}
	
	/**
	 * Returns the layers properties.
	 * 
	 * @since 0.7.1
	 * 
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}
	
	/**
	 * Returns an array of parameter definitions.
	 * 
	 * @since 0.7.1
	 * 
	 * @return array
	 */
	protected function getParameterDefinitions() {
		$params = array();
		
		$params[] = new Parameter( 'label' );
		
		$params[] = new Parameter( 'lowerbound', Parameter::TYPE_INTEGER );
		$params[] = new Parameter( 'upperbound', Parameter::TYPE_INTEGER );
		$params[] = new Parameter( 'leftbound', Parameter::TYPE_INTEGER );
		$params[] = new Parameter( 'rightbound', Parameter::TYPE_INTEGER );
		$params[] = new Parameter( 'width', Parameter::TYPE_INTEGER );
		$params[] = new Parameter( 'height', Parameter::TYPE_INTEGER );
		
		$params[] = new Parameter( 'zoomlevels', Parameter::TYPE_INTEGER, false );
		
		$params['source'] = new Parameter( 'source' );
		//$params['source']->addCriteria();
		//$params['source']->addManipulations();
		
		return $params;
	}
	
	/**
	 * Validates the layer.
	 * 
	 * @since 0.7.1
	 */
	protected function validate() {
		$validator = new Validator();
		
		$validator->setParameters( $this->properties, $this->getParameterDefinitions() );
		$validator->validateParameters();
		
		if ( $validator->hasFatalError() !== false ) {
			$this->errors = $validator->getErrors();
		}
	}
	
	/**
	 * Gets if the properties make up a valid layer definition.
	 * 
	 * @since 0.7.1
	 * 
	 * @return boolean
	 */
	public function isValid() {
		if ( !$this->hasValidated ) {
			$this->validate();
			$this->hasValidated = true;
		}
		
		return count( $this->errors ) == 0;
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
