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

		return true;
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
			$this->printer->setInputTypeHook( $inputName, 'smfSelectEditorFormInputHTML', $field_args );
		} else {
			$this->printer->setInputTypeHook( $inputName, 'smfSelectFormInputHTML', $field_args );
		}
	}
	
}
