<?php

/**
 * File holding interface iMapParserFunction.
 * 
 * @file Maps_iMapParserFunction.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 * 
 * TODO: revise this interface
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Interface that should be implemented by all mapping feature classes.
 * 
 * @author Jeroen De Dauw
 */
interface iMapParserFunction {
	function __construct( MapsMappingService $service );
	
	function getMapHtml( Parser &$parser, array $params );
	
	/**
	 * Adds the HTML specific to the mapping service to the output.
	 */
	function addSpecificMapHTML();
	
	function getSpecificParameterInfo();
}