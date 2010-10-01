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
 * @author Jeroen De Dauw
 */
class MapsFinddestination extends ParserHook {
	
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
	protected function getParameterInfo() {
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation, $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;	 
		
		$params = array();
		
		$params['location'] = new Parameter( 'location' );
		$params['location']->addCriteria( new CriterionIsLocation() );
		$params['location']->lowerCaseValue = false;
		
		$params['bearing'] = new Parameter(
			'bearing',
			Parameter::TYPE_FLOAT
		);
		
		$params['distance'] = new Parameter( 'distance' );
		$params['distance']->addCriteria( new CriterionIsDistance() );
		
		$params['mappingservice'] = new Parameter(
			'mappingservice', 
			Parameter::TYPE_STRING,
			'', // TODO
			array(),
			array(
				new CriterionInArray( MapsMappingServices::getAllServiceValues() ),
			)
		);
		
		$params['geoservice'] = new Parameter(
			'geoservice', 
			Parameter::TYPE_STRING,
			$egMapsDefaultGeoService,
			array( 'service' ),
			array(
				new CriterionInArray( $egMapsAvailableGeoServices ),
			)
		);	

		$params['allowcoordinates'] = new Parameter(
			'allowcoordinates', 
			Parameter::TYPE_BOOLEAN,
			$egMapsAllowCoordsGeocoding
		);			
		
		$params['format'] = new Parameter(
			'format',
			Parameter::TYPE_STRING,
			$egMapsCoordinateNotation,
			array( 'notation' ),
			array(
				new CriterionInArray( $egMapsAvailableCoordNotations ),
			)			
		);		
		
		$params['directional'] = new Parameter(
			'directional',
			Parameter::TYPE_BOOLEAN,
			$egMapsCoordinateDirectional			
		);			
		
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
	protected function getDefaultParameters() {
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
			global $egValidatorFatalLevel;
			switch ( $egValidatorFatalLevel ) {
				case Validator_ERRORS_NONE:
					$output = '';
					break;
				case Validator_ERRORS_WARN:
					$output = '<b>' . htmlspecialchars( wfMsgExt( 'validator_warning_parameters', array( 'parsemag' ), 1 ) ) . '</b>';
					break;
				case Validator_ERRORS_SHOW: default:
					// Show an error that the location could not be geocoded or the coordinates where not recognized.
					if ( $canGeocode ) {
						$output = htmlspecialchars( wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), $parameters['location'] ) );
					} else {
						$output = htmlspecialchars( wfMsgExt( 'maps-invalid-coordinates', array( 'parsemag' ), $parameters['location'] ) );
					}
					break;
			}
		}
			
		return $output;
	}
	
}