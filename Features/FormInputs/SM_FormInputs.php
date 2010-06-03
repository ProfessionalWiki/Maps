<?php

/**
 * Initialization file for form input functionality in the Maps extension
 *
 * @file SM_FormInputs.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['SMFormInputs'] = __FILE__;

$wgHooks['MappingFeatureLoad'][] = 'SMFormInputs::initialize';

final class SMFormInputs {
	
	public static $parameters = array();
	
	public static function initialize() {
		global $smgDir, $wgAutoloadClasses, $egMapsServices, $sfgFormPrinter;

		// This code should not get called when SF is not loaded, but let's have this
		// check to not run into problems when people mess up the settings.
		if ( !defined( 'SF_VERSION' ) ) return true;
		
		$wgAutoloadClasses['SMFormInput'] = dirname( __FILE__ ) . '/SM_FormInput.php';
		
		$hasFormInputs = false;
		
		self::initializeParams();
		
		foreach ( $egMapsServices as $serviceName => $serviceData ) {
			// Check if the service has a form input
			$hasFI = array_key_exists( 'fi', $serviceData['features'] );
			
			// If the service has no FI, skipt it and continue with the next one.
			if ( !$hasFI ) continue;
			
			// At least one form input will be enabled when this point is reached.
			$hasFormInputs = true;

			// Add the result form input type for the service name.
			self::initFormHook( $serviceName, $serviceName );
			
			// Loop through the service alliases, and add them as form input types.
			foreach ( $serviceData['aliases'] as $alias ) self::initFormHook( $alias, $serviceName );
		}
		
		// Add the 'map' form input type if there are mapping services that have FI's loaded.
		if ( $hasFormInputs ) self::initFormHook( 'map' );
		
		return true;
	}
	
	private static function initializeParams() {
		global $egMapsAvailableServices, $egMapsDefaultServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
		global $smgFIWidth, $smgFIHeight;
		
		self::$parameters = array(
			'width' => array(
				'default' => $smgFIWidth
			),
			'height' => array(
				'default' => $smgFIHeight
			),		
			'centre' => array(
				'aliases' => array( 'center' ),
			),
			'geoservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
			'service_name' => array(),
			'part_of_multiple' => array(),
			'possible_values' => array(
				'type' => array( 'string', 'array' ),
			),
			'is_list' => array(),
			'semantic_property' => array(),
			'value_labels' => array(),
		);
	}
	
	/**
	 * Adds a mapping service's form hook.
	 *
	 * @param string $inputName The name of the form input.
	 * @param strig $mainName
	 */
	private static function initFormHook( $inputName, $mainName = '' ) {
		global $wgAutoloadClasses, $sfgFormPrinter, $smgDir;
		
		// Add the form input hook for the service.
		$field_args = array();
		if ( strlen( $mainName ) > 0 ) $field_args['service_name'] = $mainName;
		$sfgFormPrinter->setInputTypeHook( $inputName, 'smfSelectFormInputHTML', $field_args );
	}
	
}

/**
 * Calls the relevant form input class depending on the provided service.
 *
 * @param string $coordinates
 * @param string $input_name
 * @param boolean $is_mandatory
 * @param boolean $is_disabled
 * @param array $field_args
 * 
 * @return array
 */
function smfSelectFormInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, array $field_args ) {
    global $egMapsServices;
    
	// Get the service name from the field_args, and set it to null if it doesn't exist.
    if ( array_key_exists( 'service_name', $field_args ) ) {
        $service_name = $field_args['service_name'];
    }
    else {
        $service_name = null;
    }
    
    // Ensure the service is valid and create a new instance of the handling form input class.
    $service_name = MapsMapper::getValidService( $service_name, 'fi' );
    $formInput = new $egMapsServices[$service_name]['features']['fi']();
    
    // Get and return the form input HTML from the hook corresponding with the provided service.
    return $formInput->formInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args );
}