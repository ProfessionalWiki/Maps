<?php

/**
 * Class for the 'finddestination' parser hooks, which can find a
 * destination given a starting point, an initial bearing and a distance.
 * 
 * @since 0.7
 * 
 * @file Maps_Finddestination.php
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsFinddestination extends ParserHook {
	/**
	 * No LSB in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */	
	public static function staticInit( Parser &$parser ) {
		$instance = new self;
		return $instance->init( $parser );
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
		return 'finddestination';
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * @see ParserHook::getParameterInfo
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
		global $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation, $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;	 
		
		$params = array();

		$params['location'] = array(
			'dependencies' => array( 'mappingservice', 'geoservice' ),
			// new CriterionIsLocation() FIXME
		);

		$params['format'] = array(
			'default' => $egMapsCoordinateNotation,
			'values' => $egMapsAvailableCoordNotations,
			'aliases' => 'notation',
			// new ParamManipulationFunctions( 'strtolower' ) FIXME
		);

		$params['directional'] = array(
			'type' => 'boolean',
			'default' => $egMapsCoordinateDirectional,
		);

		$params['bearing'] = array(
			'type' => 'float',
		);

		$params['distance'] = array(
			'type' => 'float',
			// new CriterionIsDistance() FIXME
		);

		$params['mappingservice'] = array(
			'default' => '',
			'values' => MapsMappingServices::getAllServiceValues(),
			// new ParamManipulationFunctions( 'strtolower' ) FIXME
		);

		$params['geoservice'] = array(
			'default' => $egMapsDefaultGeoService,
			'aliases' => 'service',
			'values' => $egMapsAvailableGeoServices,
			// new ParamManipulationFunctions( 'strtolower' ) FIXME
		);

		$params['directional'] = array(
			'type' => 'boolean',
			'default' => $egMapsAllowCoordsGeocoding,
		);

		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-finddestination-par-' . $name;
		}

		return $params;
	}
	
	/**
	 * Returns the list of default parameters.
	 * @see ParserHook::getDefaultParameters
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return array( 'location', 'bearing', 'distance' );
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
		$canGeocode = MapsGeocoders::canGeocode();
			
		if ( $canGeocode ) {
			$location = MapsGeocoders::attemptToGeocode(
				$parameters['location'],
				$parameters['geoservice'],
				$parameters['mappingservice']
			);
		} else {
			$location = MapsCoordinateParser::parseCoordinates( $parameters['location'] );
		}
		
		// TODO
		if ( $location ) {
			$destination = MapsGeoFunctions::findDestination(
				$location,
				$parameters['bearing'],
				MapsDistanceParser::parseDistance( $parameters['distance'] )
			);
			$output = MapsCoordinateParser::formatCoordinates( $destination, $parameters['format'], $parameters['directional'] );
		} else {
			// The location should be valid when this method gets called.
			throw new MWException( 'Attempt to find a destination from an invalid location' );
		}
			
		return $output;
	}

	/**
	 * @see ParserHook::getMessage()
	 * 
	 * @since 1.0
	 */
	public function getMessage() {
		return 'maps-finddestination-description';
	}	
	
}