<?php

/**
 * Parameter criterion stating that the value must be a google overlay.
 * 
 * @since 0.7
 * 
 * @file CriterionGoogleOverlay.php
 * @ingroup Maps
 * @ingroup Criteria
 * @ingroup MapsGoogleMaps
 * 
 * @author Jeroen De Dauw
 */
class CriterionGoogleOverlay extends ItemParameterCriterion {
	
	/**
	 * A list of supported overlays.
	 * 
	 * @since 0.7
	 * 
	 * @var array
	 */
	protected $overlayData;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7
	 * 
	 * @param array $overlayData
	 */
	public function __construct( array $overlayData ) {
		parent::__construct();
		
		$this->overlayData = $overlayData;
	}
	
	/**
	 * @see ItemParameterCriterion::validate
	 */	
	protected function doValidation( $value, Parameter $parameter, array $parameters ) {
		$value = explode( '-', $value );
		
		return
			in_array( $value[0], array_keys( $this->overlayData ) )
			|| count( $value ) == 2 && in_array( $value[1], array( '0', '1' ) )
		;
	}	
	
	/**
	 * @see ItemParameterCriterion::getItemErrorMessage
	 */	
	protected function getItemErrorMessage( Parameter $parameter ) {
		// TODO
		return wfMsgExt( '', 'parsemag', $parameter->getOriginalName() );
	}
	
	/** 
	 * @see ItemParameterCriterion::getListErrorMessage
	 */	
	protected function getListErrorMessage( Parameter $parameter, array $invalidItems ) {
		global $wgLang;
		// TODO
		return wfMsgExt( '', 'parsemag', $wgLang->listToText( $invalidItems ), count( $invalidItems ) );
	}		
	
}
