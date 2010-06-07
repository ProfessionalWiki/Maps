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
	
	/**
	 * Initialize the parser functions feature. This function handles the parser function hook,
	 * and will load the required classes.
	 */
	public static function initialize() {
		global $egMapsDir, $egMapsFeatures;
		
		include_once dirname( __FILE__ ) . '/Maps_iMapParserFunction.php';
		
		// This runs a small hook that enables parser functions to run initialization code.
		foreach ( $egMapsFeatures['pf'] as $hook ) {
			if ( strpos( $hook, '::' ) !== false ) {
				$hook = explode( '::', $hook );
			}
			
			call_user_func( $hook );
		}
		
		return true;
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
        
		foreach ( $args as $arg ) {
			$split = explode( '=', $arg );
			$name = strtolower( trim( array_shift( $split ) ) );
			if ( count( $split ) > 0 && self::inParamAliases( $name, 'service', MapsMapper::getCommonParameters() ) ) {
				if ( !$setService ) {
					$service = implode( '=', $split );
					$parameters[] = 'service=' . $service;
					$setService = true;
				}
			} else {
				$parameters[] = $arg;
			}
		}
		
		$service = MapsMapper::getValidService( $setService ? $service : '', $parserFunction );
		$mapClass = new $egMapsServices[$service]['features'][$parserFunction]();
		
		$manager = new ValidatorManager();
		
		/*
		 * Assembliy of the allowed parameters and their information. 
		 * The main parameters (the ones that are shared by everything) are overidden
		 * by the feature parameters (the ones specific to a feature). The result is then
		 * again overidden by the service parameters (the ones specific to the service),
		 * and finally by the specific parameters (the ones specific to a service-feature combination).
		 */
		$parameterInfo = array_merge_recursive( MapsMapper::getCommonParameters(), $mapClass->getFeatureParameters() );
		$parameterInfo = array_merge_recursive( $parameterInfo, $egMapsServices[$service]['parameters'] );
		$parameterInfo = array_merge_recursive( $parameterInfo, $mapClass->getSpecificParameterInfo() );
		
		$displayMap = $manager->manageParameters(
			$parameters,
			$parameterInfo,
			array( 'coordinates' )
		);
		
        if ( $displayMap ) {
            // Call the function according to the map service to get the HTML output.
            $output = $mapClass->getMapHtml( $parser, $manager->getParameters( false ) ) . $manager->getErrorList();
        } else {
        	// TODO: Get failiures
        	if ( $egValidatorFatalLevel == Validator_ERRORS_WARN ) {
        		$output .= htmlspecialchars( wfMsg( '' ) );
        	} elseif ( $egValidatorFatalLevel > Validator_ERRORS_WARN ) {
        		$output .= htmlspecialchars( wfMsg( '' ) );
        	}
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

		if ( !$equals && array_key_exists( $mainParamName, $paramInfo ) ) {
			$equals = in_array( $name, $paramInfo[$mainParamName] );
		}

		return $equals;
	}
}
