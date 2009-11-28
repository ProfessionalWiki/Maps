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
	
	// TODO: add lower/upper case nullification
	
	/**
	 * @var boolean Indicates whether parameters not found in the criteria list
	 * should be stored in case they are not accepted. The default is false.
	 */	
	public static $storeUnknownParameters = false;
	
	/**
	 * @var boolean Indicates whether parameters not found in the criteria list
	 * should be stored in case they are not accepted. The default is false.
	 */		
	public static $accumulateParameterErrors = false;
	
	/**
	 * @var array Holder for the validation functions.
	 */		
	private static $validationFunctions = array(
			'in_array' => 'in_array',
			'in_range' => array('MapsValidationFunctions', 'in_range'),
			'is_numeric' => 'is_numeric', 
			'not_empty' => array('MapsValidationFunctions', 'not_empty'), 
			'all_in_array' => array('MapsValidationFunctions', 'all_in_array'), 
			'any_in_array' => array('MapsValidationFunctions', 'any_in_array'), 	
			);
	
	private $parameterInfo;
	private $rawParameters = array();
	
	private $valid = array();
	private $invalid = array();	
	private $unknown = array();

	private $errors = array();
	
	/**
	 * Sets the parameter criteria, used to valiate the parameters.
	 * 
	 * @param array $parameterInfo
	 */
	public function setParameterInfo(array $parameterInfo) {
		$this->parameterInfo = $parameterInfo;
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
	 * @return boolean Indicates whether there where no errors.
	 */
	public function validateParameters() {	

		$parameters = array();		
		
		// Loop through all the user provided parameters, and destinguise between those that are allowed and those that are not.
		foreach($this->rawParameters as $paramName => $paramValue) {
			// Attempt to get the main parameter name (takes care of aliases).
			$mainName = MapsParamValidator::getMainParamName($paramName, $this->parameterInfo);
			// If the parameter is found in the list of allowed ones, add it to the $parameters array.
			if($mainName) { 
				$parameters[$mainName] = $paramValue;
			}
			else { // If the parameter is not found in the list of allowed ones, add an item to the $this->errors array.
				if (MapsParamValidator::$storeUnknownParameters) $this->unknown[$paramName] = $paramValue;
				$this->errors[] = array('error' => array('unknown'), 'name' => $paramName);				
			}
		}

		// Loop through the list of allowed parameters.
		foreach($this->parameterInfo as $paramName => $paramInfo) {
			// If the user provided a value for this parameter, validate and handle it.
			if (array_key_exists($paramName, $this->rawParameters)) {
				
				$paramValue = $this->rawParameters[$paramName];
				$validationErrors = $this->validateParameter($paramName, $paramValue);
				
				if (count($validationErrors) == 0) {
					$this->valid[$paramName] = $paramValue;
				}
				else {
					$this->invalid[$paramName] = $paramValue;
					foreach($validationErrors as $error) {
						$this->errors[] = array('error' => $error, 'name' => $paramName);
					}
				}				
			}
			else { // If the user did not provide a value for this parameter, set the default if there is one.
				if (array_key_exists('default', $paramInfo)) {
					$this->valid[$paramName] = $paramInfo['default'];
				}
				else { // If there is no default, the parameter must be provided, so add an error.
					$this->errors[] = array('error' => array('missing'), 'name' => $paramName);	
				}
			}
		}
		
		return count($this->errors) == 0;
	}
	
	/**
	 * Returns the main parameter name for a given parameter or alias, or false
	 * when it is not recognized as main parameter or alias.
	 *
	 * @param string $paramName
	 * @param array $allowedParms
	 * 
	 * @return string
	 */
	private function getMainParamName($paramName, array $allowedParms) {
		$result = false;
		
		if (array_key_exists($paramName, $allowedParms)) {
			$result = $paramName;
		}
		else {
			foreach ($allowedParms as $name => $data) {
				if (array_key_exists('aliases', $data)) {
					if (in_array($paramName, $data['aliases'])) {
						$result = $name;
						break;
					}					
				}
			}
		}
		
		return $result;
	}		
	
	/**
	 * Valides the provided parameter by matching the value against the criteria for the name.
	 * 
	 * @param string $name
	 * @param string $value
	 * 
	 * @return array The errors that occured during validation.
	 */
	private function validateParameter($name, $value) {
		$errors = array();
		
		if (array_key_exists('criteria', $this->parameterInfo[$name])) {
			foreach($this->parameterInfo[$name]['criteria'] as $criteriaName => $criteriaArgs) {
				$validationFunction = MapsParamValidator::$validationFunctions[$criteriaName];
				$arguments = array($value);
				if (count($criteriaArgs) > 0) $arguments[] = $criteriaArgs;
				$isValid = call_user_func_array($validationFunction, $arguments);	
				
				if (! $isValid) {
					$errors[] = array($criteriaName, $criteriaArgs, $value);
					if (! MapsParamValidator::$accumulateParameterErrors) break;
				}
			}			
		}

		return $errors;
	}
	
	/**
	 * Changes the invalid parameters to their default values, and changes their state to valid.
	 */
	public function correctInvalidParams() {
		foreach($this->invalid as $paramName => $paramValue) {
			
			if(array_key_exists('default', $this->parameterInfo[$paramName])) {
				unset($this->invalid[$paramName]);
				$this->valid[$paramName] = $this->parameterInfo[$paramName]['default'];
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
		return $this->valid;
	}
	
	/**
	 * Returns the unknown parameters. 
	 * 
	 * @return array
	 */	
	public static function getUnknownParams() {
		return $this->unknown;
	}
	
	/**
	 * Returns the errors. 
	 * 
	 * @return array
	 */
	public function getErrors() {
		return $this->errors;
	}
	
	/**
	 * Adds a new criteria type and the validation function that should validate values of this type.
	 * You can use this function to override existing criteria type handlers.
	 * 
	 * @param string $criteriaName The name of the cirteria.
	 * @param array $functionName The functions location. If it's a global function, only the name,
	 * if it's in a class, first the class name, then the method name. 
	 */
	public static function addValidationFunction($criteriaName, array $functionName) {
		$this->validationFunctions[$criteriaName] = $functionName;
	}
	
}