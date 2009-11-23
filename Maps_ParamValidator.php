<?php

/** 
 * File holding the MapsParamValidator class.
 *
 * @file Maps_ParamValidator.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/** 
 * Class for parameter validation.
 * 
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
final class MapsParamValidator {
	
	/**
	 * @var boolean Indicates whether parameters not found in the criteria list
	 * should be accepted. The default is false.
	 */
	public static $acceptUnknownParameters = false;
	
	/**
	 * @var boolean Indicates whether parameters not found in the criteria list
	 * should be stored in case they are not accepted. The default is false.
	 */	
	public static $storeUnknownParameters = false;
	
	private $parameterCriteria;
	private $rawParameters = array();
	
	private $valid = array();
	private $invalid = array();	
	private $unknown = array();

	private $errors = array();
	
	/**
	 * Sets the parameter criteria, used to valiate the parameters.
	 * 
	 * @param array $parameterCriteria
	 */
	public function setCriteria(array $parameterCriteria) {
		$this->parameterCriteria = $parameterCriteria;
	} 	
	
	/**
	 * Sets the raw parameters that will be validated when validateParameters is called.
	 * 
	 * @param array $parameters
	 */
	public function setParameters(array $parameters) {
		$this->rawParameters = $parameters;
	} 
	
	/**
	 * Valides the raw parameters, and allocates them as valid, invalid or unknown.
	 * Errors are collected, and can be retrieved via getErrors.
	 * 
	 * @return boolean Indicates whether there where any errors.
	 */
	public function validateParameters() {	
		foreach($this->rawParameters as $paramName => $paramValue) {
			$paramName = MapsMapper::getMainParamName($paramName, $allowedParms);  // TODO: get $allowedParms
			if(array_key_exists($paramName, $allowedParms)) { 
				
				$validationResult = $this->validateParameter($paramName, $paramValue);
				
				if ($validationResult === true) {
					$this->valid[$paramName] = $paramValue;
				}
				else {
					$this->invalid[$paramName] = $paramValue;
					$this->errors[] = array('type' => $validationResult, 'name' => $paramName);
				}
			}
			else {
				if ($storeUnknownParameters) $this->unknown[$paramName] = $paramValue;
				$this->errors[] = array('type' => 'unknown', 'name' => $paramName);
			}
		}
		
		return count($this->errors) > 0;
	}
	
	/**
	 * Valides the provided parameter by matching the value against the criteria for the name.
	 * 
	 * @param string $name
	 * @param string $value
	 * 
	 * @return true or string
	 */
	private function validateParameter($name, $value) {
		
		// TODO: get criteria and validate
		// If valid: return true
		// If infalid: return error type
		
	}
	
	/**
	 * Changes the invalid parameters to their default values, and changes their state to valid.
	 * 
	 * @param array $defaults
	 */
	public function correctInvalidParams(array $defaults) {
		foreach($this->invalid as $paramName => $paramValue) {
			
			if(array_key_exists($paramName, $defaults)) {
				unset($this->invalid[$paramName]);
				$this->valid[$paramName] = $defaults[$defaults];
			}
			else {
				throw new Exception('The default value for parameter ' . $paramName . ' is not set.');
			}
		}		
	}
	
	/**
	 * Returns the valid parameters. 
	 * 
	 * @return array
	 */
	public function getValidParams() {
		return $this->valid();
	}
	
	/**
	 * Returns the errors. 
	 * 
	 * @return array
	 */
	public function getErrors() {
		return $this->errors();
	}
	
}