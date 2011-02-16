<?php

/**
 * Parameter manipulation ensuring the value is an image url.
 * 
 * @since 0.7.1
 * 
 * @file Maps_ParamImage.php
 * @ingroup Maps
 * @ingroup ParameterManipulations
 * 
 * @author Jeroen De Dauw
 */
class MapsParamImageFull extends ItemParameterManipulation {

	/**
	 * Constructor.
	 * 
	 * @since 0.7
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @see ItemParameterManipulation::doManipulation
	 * 
	 * @since 0.7
	 */	
	public function doManipulation( &$value, Parameter $parameter, array &$parameters ) {
		$image = $value;
		$title = Title::newFromText( $image, NS_FILE );

		if ( !is_null( $title ) && $title->getNamespace() == NS_FILE && $title->exists() ) {
			$imagePage = new ImagePage( $title );
			$image = $imagePage->getDisplayedFile()->getFullUrl();
		}		
		$value = $image;
	}
	
}