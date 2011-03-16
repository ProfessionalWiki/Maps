<?php

/**
 * Google Maps v3 form input class.
 *
 * @since 0.8
 * @file SM_GoogleMaps3FormInput.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMGoogleMaps3FormInput extends SMFormInput {
	
	/**
	 * @see SMFormInput::getResourceModules
	 * 
	 * @since 0.8
	 * 
	 * @return array of string
	 */
	protected function getResourceModules() {
		return array_merge( parent::getResourceModules(), array( 'ext.sm.fi.googlemaps3.single' ) );
	}	
	
}
