<?php

/**
 * Initialization file for parser function functionality in the Maps extension
 *
 * @file Maps_ParserFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsParserFunctions'] = __FILE__;

$wgHooks['MappingFeatureLoad'][] = 'MapsParserFunctions::initialize';

/**
 * A class that holds handlers for the mapping parser functions.
 * 
 * @author Jeroen De Dauw
 */
final class MapsParserFunctions {
	
	public static $parameters = array();
	
	/**
	 * Initialize the parser functions feature. This function handles the parser function hook,
	 * and will load the required classes.
	 */
	public static function initialize() {
		global $egMapsDir, $IP, $wgAutoloadClasses, $egMapsFeatures, $egMapsServices;
		
		include_once $egMapsDir . 'ParserFunctions/Maps_iDisplayFunction.php';
		
		// This runs a small hook that enables parser functions to run initialization code.
		foreach ( $egMapsFeatures['pf'] as $hook ) {
			call_user_func( $hook );
		}
		
		return true;
	}
	
	private static function initializeParams() {
		global $egMapsAvailableServices, $egMapsDefaultServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
	}
	
	/**
	 * Returns the output for the call to the specified parser function.
	 * 
	 * @param Parser $parser
	 * @param array $params
	 * @param string $parserFunction
	 * 
	 * @return array
	 */
	public static function getMapHtml( Parser &$parser, array $args, $parserFunction ) {
        global $wgLang, $egValidatorErrorLevel, $egValidatorFatalLevel, $egMapsServices;
        
        array_shift( $args ); // We already know the $parser.
        
        $parameters = array();
        $setService = false; 
        
		foreach( $args as $arg ) {
			$split = explode( '=', $arg );
			$name = strtolower( trim( array_shift( $split ) ) );
			if ( count( $split ) > 1 && self::inParamAliases( $name, 'service', self::$parameters ) ) {
				if ( !$setService ) {
					$service = implode( '=', $split );
					$parameters = 'service=' . $service;
					$setService = true;					
				}
			} else {
				$parameters[] = $arg;
			}
		}
		
		$service = MapsMapper::getValidService( $setService ? $service : '', $parserFunction );
		
		// TODO: hook into Validator for main parameter validation, geocoding and coordinate parsing
		
		$mapClass = new $egMapsServices[$service]['features'][$parserFunction]();
		
		$manager = new ValidatorManager();
		
		/*
		 * Assembliy of the allowed parameters and their information. 
		 * The main parameters (the ones that are shared by everything) are overidden
		 * by the feature parameters (the ones spesific to a feature). The result is then
		 * again overidden by the service parameters (the ones spesific to the service),
		 * and finally by the spesific parameters (the ones spesific to a service-feature combination).
		 */
		$parameterInfo = array_merge( MapsMapper::getMainParams(), self::$parameters );
		$parameterInfo = array_merge( $parameterInfo, $mapClass->getFeatureParameters() );
		$parameterInfo = array_merge( $parameterInfo, $egMapsServices[$service]['parameters'] );
		$parameterInfo = array_merge( $parameterInfo, $mapClass->getSpecificParameterInfo() ); 
		
		$parameters = $manager->manageParameters(
			$parameters,
			$parameterInfo,
			array( 'coordinates' )
		);        
        
		$displayMap = $parameters !== false;
		
        if ( $displayMap ) {
            // Call the function according to the map service to get the HTML output.
            $output = $mapClass->displayMap( $parser, $parameters ) . $manager->getErrorList();
        } else {
			// TODO: add errors to output depending on validator fatal level 	
        }
        
        // Return the result.
        return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	
	/**
	 * Gets if a provided name is present in the aliases array of a parameter
	 * name in the $mainParams array.
	 *
	 * @param string $name The name you want to check for.
	 * @param string $mainParamName The main parameter name.
	 * @param array $paramInfo Contains meta data, including aliases, of the possible parameters.
	 * @param boolean $compareMainName Boolean indicating wether the main name should also be compared.
	 * 
	 * @return boolean
	 */
	public static function inParamAliases( $name, $mainParamName, array $paramInfo = array(), $compareMainName = true ) {
		$equals = $compareMainName && $mainParamName == $name;

		if ( array_key_exists( $mainParamName, $paramInfo ) ) {
			$equals = $equals || in_array( $name, $paramInfo[$mainParamName] );
		}

		return $equals;
	}
}