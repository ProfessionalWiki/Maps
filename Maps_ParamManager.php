<?php

/** 
 * File holding the MapsParamManager class.
 *
 * @file Maps_ParamManager.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/** 
 * Class for parameter handling.
 * 
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
final class MapsParamManager {
	
	private $errors = array();
	
	/**
	 * Validates the provided parameters, and corrects them depedning on the error level.
	 * 
	 * @param array $rawParameters
	 * @param array $parameterInfo 
	 * 
	 * @return boolean Indicates whether the regular output should be shown or not.
	 */
	public function manageMapparameters(array $rawParameters, array $parameterInfo) {
		global $egMapsErrorLevel;
		
		$validator = new MapsParamValidator();
		
		$validator->setParameterInfo($parameterInfo);
		$validator->setParameters($rawParameters);
		
		if (! $validator->validateParameters()) {
			if ($egMapsErrorLevel != Maps_ERRORS_STRICT) $validator->correctInvalidParams();
			if ($egMapsErrorLevel >= Maps_ERRORS_SHOW) $this->errors = $validator->getErrors(); 
		}

		$showOutput = ! ($egMapsErrorLevel == Maps_ERRORS_STRICT && count($this->errors) > 0);
		
		return $showOutput ? $validator->getValidParams() : false;
	}
	
	/**
	 * Returns a string containing an HTML error list, or an empty string when there are no errors. 
	 * 
	 * @return string
	 */
	public function getErrorList() {
		global $wgLang;
		global $egMapsErrorLevel;
		
		if ($egMapsErrorLevel >= Maps_ERRORS_SHOW && count($this->errors) > 0) {
			$errorList = '<b>' . wfMsg('maps_error_parameters') . ':</b><br /><i>';
			
			$errors = array();
			
			foreach($this->errors as $error) {
				$error['name'] = '</i>' . $error['name'] . '<i>';
				switch($error['error'][0]) {
					// General errors
					case 'unknown' :
						$errors[] = wfMsgExt('maps_error_unknown_argument', array('parsemag'), $error['name']);
						break;
					case 'missing' :
						$errors[] = wfMsgExt('maps_error_required_missing', array('parsemag'), $error['name']);
						break;		
					// Spesific validation faliures
					case 'not_empty' :
						$errors[] = wfMsgExt('maps_error_empty_argument', array('parsemag'), $error['name']);
						break;						
					case 'in_range' :
						$errors[] = wfMsgExt('maps_error_ivalid_range', array('parsemag'), $error['name'], $error['error'][1][0], $error['error'][1][1]);
						break;		
					case 'is_numeric' :
						$errors[] = wfMsgExt('maps_error_must_be_number', array('parsemag'), $error['name']);
						break;		
					case 'in_array' :
						$items = $wgLang->listToText($error['error'][1]);
						$errors[] = wfMsgExt('maps_error_accepts_only', array('parsemag'), $error['name'], $items);
						break;	
					// Unspesified errors
					case 'invalid' : default :
						$errors[] = wfMsgExt('maps_error_invalid_argument', array('parsemag'), $error['error'][2], $error['name']); 
						break;																	
				}
			}
			
			return $errorList. implode($errors, '<br />') . '</i>';
		}
		else {
			return '';
		}
	}
	
}