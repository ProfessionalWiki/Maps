<?php

/**
 * Class for the 'geocode' parser hooks, which can turn
 * human readable locations into sets of coordinates.
 * 
 * @since 0.7
 * 
 * @file Maps_Geocode.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsGeocode extends ParserHook {
	
	/**
	 * No LST in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */
	public static function staticMagic( array &$magicWords, $langCode ) {
		$className = __CLASS__;
		$instance = new $className();
		return $instance->magic( $magicWords, $langCode );
	}
	
	/**
	 * No LST in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */	
	public static function staticInit( Parser &$wgParser ) {
		$className = __CLASS__;
		$instance = new $className();
		return $instance->init( $wgParser );
	}	
	
	/**
	 * Gets the name of the parser hook.
	 * @see ParserHook::getName
	 * 
	 * @since 0.7
	 * 
	 * @return string
	 */
	protected function getName() {
		return 'geocode';
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * @see ParserHook::getParameterInfo
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getParameterInfo() {
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsAvailableCoordNotations;
		global $egMapsDefaultServices, $egMapsDefaultGeoService, $egMapsCoordinateNotation;
		global $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;
				
		return array(
			'location' => array(
				'required' => true,
				'tolower' => false
			),
			'mappingservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableServices
				),
				'default' => false
			),
			'service' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
			'format' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableCoordNotations
				),
				'aliases' => array(
					'notation'
				),
				'default' => $egMapsCoordinateNotation
			),
			'allowcoordinates' => array(
				'type' => 'boolean',
				'default' => $egMapsAllowCoordsGeocoding
			),
			'directional' => array(
				'type' => 'boolean',
				'default' => $egMapsCoordinateDirectional
			),
		);
	}
	
	/**
	 * Returns the list of default parameters.
	 * @see ParserHook::getDefaultParameters
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getDefaultParameters() {
		return array( 'location', 'service', 'mappingservice' );
	}
	
	/**
	 * Renders and returns the output.
	 * @see ParserHook::render
	 * 
	 * @since 0.7
	 * 
	 * @param array $parameters
	 * 
	 * @return string
	 */
	public function render( array $parameters ) {
		if ( MapsGeocoders::canGeocode() ) {
			$geovalues = MapsGeocoders::attemptToGeocodeToString(
				$parameters['location'],
				$parameters['service'],
				$parameters['mappingservice'],
				$parameters['allowcoordinates'],
				$parameters['format'],
				$parameters['directional']
			);
			
			$output = $geovalues ? $geovalues : '';
		}
		else {
			$output = htmlspecialchars( wfMsg( 'maps-geocoder-not-available' ) );
		}
		
		return $output;		
	}
	
}