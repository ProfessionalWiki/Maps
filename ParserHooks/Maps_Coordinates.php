<?php

/**
 * This file contains registration for the #coordinates parser function,
 * 
 * 
 * @file Maps_Coordinates.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsCoordinates'] = dirname( __FILE__ ) . '/Maps_Coordinates.php';

$wgHooks['ParserFirstCallInit'][] = 'MapsCoordinates::init';

if ( version_compare( $wgVersion, '1.16alpha', '<' ) ) {
	$wgHooks['LanguageGetMagic'][] = 'MapsCoordinates::magic';
}

/**
 * Class for the 'coordinates' parser hooks, 
 * which can transform the notation of a set of coordinates.
 * 
 * @since 0.7
 * 
 * @author Jeroen De Dauw
 */
class MapsCoordinates {
	
	/**
	 * Function to hook up the coordinate rendering functions to the parser.
	 * 
	 * @since 0.7
	 * 
	 * @param Parser $wgParser
	 * 
	 * @return true
	 */
	public static function init( Parser &$wgParser ) {
		$wgParser->setHook( 'coordinates', __CLASS__ . '::renderTag' );
		$wgParser->setFunctionHook( 'coordinates', __CLASS__ . '::renderFunction' );
		
		return true;
	}
	
	/**
	 * Function to add the magic word in pre MW 1.16.
	 * 
	 * @since 0.7
	 * 
	 * @param array $magicWords
	 * @param string $langCode
	 * 
	 * @return true
	 */
	public static function magic( array &$magicWords, $langCode ) {
		$magicWords['coordinates'] = array( 0, 'coordinates' );
		
		return true;
	}	
	
	/**
	 * Handler for rendering the tag hook.
	 * 
	 * @since 0.7
	 * 
	 * @param minxed $input string or null
	 * @param array $args
	 * @param Parser $parser
	 * @param PPFrame $frame
	 */
	public static function renderTag( $input, array $args, Parser $parser, PPFrame $frame ) {
		$defaultParam = array_shift( self::getDefaultParameters() );
		
		if ( !is_null( $defaultParam ) ) {
			$args[$defaultParam] = $input;
		}
		
		return self::render( $args, true );
	}
	
	/**
	 * Handler for rendering the function hook.
	 * 
	 * @since 0.7
	 * 
	 * @param Parser $parser
	 * ... further arguments ...
	 */	
	public static function renderFunction() {
		$args = func_get_args();
		
		// No need for the parser...
		array_shift( $args );	
	
		return array( self::render( $args, false ) );
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected static function getParameterInfo() {
		global $egMapsAvailableServices, $egMapsAvailableCoordNotations;
		global $egMapsDefaultServices, $egMapsDefaultGeoService, $egMapsCoordinateNotation;
		global $egMapsAllowCoordsGeocoding, $egMapsCoordinateDirectional;
				
		return array(
			'location' => array(
				'required' => true,
				'tolower' => false
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
			'directional' => array(
				'type' => 'boolean',
				'default' => $egMapsCoordinateDirectional
			)
		);
	}
	
	/**
	 * Returns the list of default parameters.
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected static function getDefaultParameters() {
		return array( 'location', 'format', 'directional' );
	}
	
	/**
	 * Renders and returns the output.
	 * 
	 * @since 0.7
	 * 
	 * @param array $arguments
	 * @param boolean $parsed
	 * 
	 * @return string
	 */
	public static function render( array $arguments, $parsed ) {
		$manager = new ValidatorManager();
		
		if ( $parsed ) {
			$doFormatting = $manager->manageParsedParameters(
				$arguments,
				self::getParameterInfo(),
				self::getDefaultParameters()
			);			
		}
		else {
			$doFormatting = $manager->manageParameters(
				$arguments,
				self::getParameterInfo(),
				self::getDefaultParameters()
			);			
		}
		
		if ( $doFormatting ) {
			$parameters = $manager->getParameters( false );
			
			$parsedCoords = MapsCoordinateParser::parseCoordinates( $parameters['location'] );
			
			if ( $parsedCoords ) {
				$output = MapsCoordinateParser::formatCoordinates( $parsedCoords, $parameters['format'], $parameters['directional'] );
			} else {
				$output = htmlspecialchars( wfMsgExt( 'maps-invalid-coordinates', 'parsemag', $parameters['location'] ) );
			}
			
			$errorList = $manager->getErrorList();
	
			if ( $errorList != '' ) {
				$output .= '<br />' . $errorList;
			}
		} else {
			$output = $manager->getErrorList();
		}
	
		return $output;		
	}
	
}