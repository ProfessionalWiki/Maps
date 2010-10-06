<?php

/**
 * Class for the 'geodistance' parser hooks, which can
 * calculate the geographical distance between two points.
 * 
 * @since 0.7
 * 
 * @file Maps_Geodistance.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsGeodistance extends ParserHook {
	
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
		return 'geodistance';
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
		global $egMapsDistanceUnit, $egMapsDistanceDecimals, $egMapsAvailableGeoServices, $egMapsDefaultGeoService; 
		
		$params = array();
		
		$params['location1'] = new Parameter(
			'location1',
			Parameter::TYPE_STRING,
			null,
			array( 'from' ),
			array(
				new CriterionIsLocation(),
			)			
		);
		$params['location1']->lowerCaseValue = false;
		$params['location1']->addDependencies( 'mappingservice', 'geoservice' );
		
		$params['location2'] = new Parameter(
			'location2',
			Parameter::TYPE_STRING,
			null,
			array( 'to' ),
			array(
				new CriterionIsLocation(),
			)			
		);
		$params['location2']->lowerCaseValue = false;
		$params['location2']->addDependencies( 'mappingservice', 'geoservice' );			
		
		$params['unit'] = new Parameter(
			'unit',
			Parameter::TYPE_STRING,
			$egMapsDistanceUnit,
			array(),
			array(
				new CriterionInArray( MapsDistanceParser::getUnits() ),
			)
		);	

		$params['decimals'] = new Parameter(
			'decimals',
			Parameter::TYPE_INTEGER,
			$egMapsDistanceDecimals
		);			
		
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
		return array( 'location1', 'location2', 'unit', 'decimals' );
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
		global $wgLang;
		
		$canGeocode = MapsMapper::geocoderIsAvailable();
		
		if ( $canGeocode ) {
			$start = MapsGeocoders::attemptToGeocode( $parameters['location1'], $parameters['geoservice'], $parameters['mappingservice'] );
			$end = MapsGeocoders::attemptToGeocode( $parameters['location2'], $parameters['geoservice'], $parameters['mappingservice'] );
		} else {
			$start = MapsCoordinateParser::parseCoordinates( $parameters['location1'] );
			$end = MapsCoordinateParser::parseCoordinates( $parameters['location2'] );
		}
		
		if ( $start && $end ) {
			$output = MapsDistanceParser::formatDistance( MapsGeoFunctions::calculateDistance( $start, $end ), $parameters['unit'], $parameters['decimals'] );
		} else {
			// TODO: use ParserHook class methods to handle errors
			global $egValidatorFatalLevel;
			
			$fails = array();
			if ( !$start ) $fails[] = $parameters['location1'];
			if ( !$end ) $fails[] = $parameters['location2'];
			
			switch ( $egValidatorFatalLevel ) {
				case Validator_ERRORS_NONE:
					$output = '';
					break;
				case Validator_ERRORS_WARN:
					$output = '<b>' . htmlspecialchars( wfMsgExt( 'validator_warning_parameters', array( 'parsemag' ), count( $fails ) ) ) . '</b>';
					break;
				case Validator_ERRORS_SHOW: default:
					global $wgLang;
					
					if ( $canGeocode ) {
						$output = htmlspecialchars( wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), $wgLang->listToText( $fails ), count( $fails ) ) );
					} else {
						$output = htmlspecialchars( wfMsgExt( 'maps_unrecognized_coords', array( 'parsemag' ), $wgLang->listToText( $fails ), count( $fails ) ) );
					}
					break;
			}
		}

		return $output;
	}
	
}