<?php

namespace SemanticMaps;

use MapsMappingServices;
use SFFormPrinter;

/**
 * Initialization file for form input functionality in the Maps extension
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FormInputsSetup {

	public static function run( SFFormPrinter $printer ) {
		$setup = new FormInputsSetup( $printer );
		$setup->initialize();
		return true;
	}

	private $printer;

	public function __construct( SFFormPrinter $printer ) {
		$this->printer = $printer;
		$this->initialize();
	}

	private function initialize() {
		$hasFormInputs = false;

		foreach ( MapsMappingServices::getServiceIdentifiers() as $serviceIdentifier ) {
			$service = MapsMappingServices::getServiceInstance( $serviceIdentifier );
			
			// Check if the service has a form input.
			$FIClass = $service->getFeature( 'fi' );
			
			// If the service has no FI, skipt it and continue with the next one.
			if ( $FIClass === false ) continue;
			
			// At least one form input will be enabled when this point is reached.
			$hasFormInputs = true;

			// Add the result form input type for the service name.
			$this->initFormHook( $service->getName(), $service->getName() );
			
			// Loop through the service alliases, and add them as form input types.
			foreach ( $service->getAliases() as $alias ) {
				$this->initFormHook( $alias, $service->getName() );
			}
		}
		
		// Add the 'map' form input type if there are mapping services that have FI's loaded.
		if ( $hasFormInputs ) {
			$this->initFormHook( 'map' );
		}
		$this->initFormHook( 'googlemapsEditor' );
	}
	
	/**
	 * Adds a mapping service's form hook.
	 *
	 * @param string $inputName The name of the form input.
	 * @param string $mainName
	 */
	private function initFormHook( $inputName, $mainName = '' ) {
		// Add the form input hook for the service.
		$field_args = array();
		
		if ( $mainName !== '' ) {
			$field_args['service_name'] = $mainName;
		}

		if( $inputName == 'googlemapsEditor' ) {
			$this->printer->setInputTypeHook( $inputName, 'SemanticMaps\FormInputsSetup::smfSelectEditorFormInputHTML', $field_args );
		} else {
			$this->printer->setInputTypeHook( $inputName, 'SemanticMaps\FormInputsSetup::smfSelectFormInputHTML', $field_args );
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
	public static function smfSelectFormInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, array $field_args ) {
		// Get the service name from the field_args, and set it to null if it doesn't exist.
		$serviceName = array_key_exists( 'service_name', $field_args ) ? $field_args['service_name'] : null;

		// Get the instance of the service class.
		$service = MapsMappingServices::getValidServiceInstance( $serviceName, 'fi' );

		// Get an instance of the class handling the current form input and service.
		$formInput = $service->getFeatureInstance( 'fi' );

		// Get and return the form input HTML from the hook corresponding with the provided service.
		return $formInput->getInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args );
	}

	/**
	 * Calls the relevant form Editor input class depending on the provided service.
	 * NOTE: Currently only GoogleMaps is supported
	 *
	 * @since 2.0
	 *
	 * @param string $coordinates
	 * @param string $input_name
	 * @param boolean $is_mandatory
	 * @param boolean $is_disabled
	 * @param array $field_args
	 *
	 * @return array
	 */
	public static function smfSelectEditorFormInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, array $field_args ) {
		// Get the service name from the field_args, and set it to null if it doesn't exist.
		$serviceName = array_key_exists( 'service_name', $field_args ) ? $field_args['service_name'] : null;
		// Get the instance of the service class.
		$service = MapsMappingServices::getValidServiceInstance( $serviceName, 'fi' );

		// Get an instance of the class handling the current form input and service.
		$formInput = $service->getFeatureInstance( 'fi' );
		// Get and return the form input HTML from the hook corresponding with the provided service.
		return $formInput->getEditorInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args );
	}


}
