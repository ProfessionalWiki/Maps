<?php

/**
 * Class to Handle Polygons in SM.
 * This class is used to convert the string representation
 * of Polygons to concrete structures.
 * Also acts as a factory class for polygons
 *
 * @since sm.polygons
 *
 * @file SM_PolygonHandler.php
 *
 * @author Nischay Nahata
 */
class PolygonHandler {

	/**
	 * The string used to store this value as a string in SMW.
	 *
	 * @since sm.polygons
	 *
	 * @var string
	 */
	protected $text;

	/**
	 * The string used to store this value as an object.
	 *
	 * @since sm.polygons
	 *
	 * @var object or null
	 */
	protected $value = null;

	/**
	 * The array of error messages occured in parsing.
	 *
	 * @since sm.polygons
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Array of classes used to validate different Geographic shapes.
	 *
	 * @since sm.polygons
	 *
	 * @var array
	 */
	protected $validatorClasses = array(
		'locations' => 'LocationValidator',
		'lines' => 'LineValidator',
		'polygons' => 'PolygonValidator',
		'circles' => 'CircleValidator',
		'rectangles' => 'RectangleValidator'
	);

	/**
	 * Array of classes of different Geographic shapes.
	 *
	 * @since sm.polygons
	 *
	 * @var array
	 */
	protected $geoClasses = array(
		'locations' => 'MapsLocation',
		'lines' => 'MapsLine',
		'polygons' => 'MapsPolygon',
		'circles' => 'MapsCircle',
		'rectangles' => 'MapsRectanlge'
	);

	/**
	 * NOTE: These need to be changed as Manipulations are depreceated.
	 * Array of classes for param handling of different Geographic shapes.
	 *
	 * @since sm.polygons
	 *
	 * @var array
	 */
	protected $paramClasses = array(
		'locations' => 'MapsParamLocation',
		'lines' => 'MapsParamLine',
		'polygons' => 'MapsParamPolygon',
		'circles' => 'MapsParamCircle',
		'rectangles' => 'MapsParamRectangle'
	);

	/**
	 * Constructor.
	 *
	 * @since sm.polygons
	 *
	 * @param string $text
	 */
	public function __construct( $text ) {
		$this->text = $text;
	}

	public function getGeoType() {
		$parts = explode( '=', $this->text );
		return current( $parts );
	}

	public function getValidationErrors() {
		$this->validateText();
		return $this->errors;
	}

	protected function validateText() {
		$parts = explode( '=', $this->text );
		if( array_key_exists( $parts[0], $this->validatorClasses ) ) {
			$validatorClass = new $this->validatorClasses[ $parts[0] ]( '~' );
			if ( !$validatorClass->doValidation( $parts[1] ) )
				$this->errors[] = 'Improper formatting of $parts[0]';
		} else {
			$this->errors[] = 'No matching geo Shape found';
		}
	}

	public function shapeFromText() {
		$parts = explode( '~' , $this->text );
		$shape = explode( '=', array_shift( $parts ) );
		if( array_key_exists( $shape[0] , $this->geoClasses ) ) {
			$geoClass = new $this->geoClasses[ $shape[0] ]( explode( ':' , $shape[1] ) );
			$paramClass = new $this->paramClasses[ $shape[0] ]( '~' );
			$paramClass->handleCommonParams( $parts , $geoClass );

			return $geoClass;
		} else {
			return false;
		}
	}
}