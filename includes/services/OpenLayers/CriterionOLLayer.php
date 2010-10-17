<?php

/**
 * Parameter criterion stating that the value must be an OpenLayers layer.
 * 
 * @since 0.7
 * 
 * @file CriterionOLLayer.php
 * @ingroup Maps
 * @ingroup Criteria
 * @ingroup MapsOpenLayers
 * 
 * @author Jeroen De Dauw
 */
class CriterionOLLayer extends ItemParameterCriterion {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @see ItemParameterCriterion::validate
	 */	
	protected function doValidation( $value, Parameter $parameter, array $parameters ) {
		return true; // TODO
	}	
	
	/**
	 * @see ItemParameterCriterion::getItemErrorMessage
	 */	
	protected function getItemErrorMessage( Parameter $parameter ) {
		return wfMsgExt( 'validation-error-invalid-ollayer', 'parsemag', $parameter->getOriginalName() );
	}
	
	/** 
	 * @see ItemParameterCriterion::getFullListErrorMessage
	 */	
	protected function getFullListErrorMessage( Parameter $parameter ) {
		return wfMsgExt( 'validation-error-invalid-ollayers', 'parsemag', $parameter->getOriginalName() );
	}		
	
}
