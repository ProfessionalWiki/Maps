<?php

/**
 * File holding interface iMapParserFunction.
 * 
 * @file Maps_iMapParserFunction.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Interface that should be implemented by all map display parser functions.
 * 
 * @author Jeroen De Dauw
 * 
 * @since 0.6.3
 */
interface iMapParserFunction extends iMappingFeature {
	
	/**
	 * Constructor.
	 * 
	 * @param MapsMappingService $service
	 */
	function __construct( MapsMappingService $service );
	
	/**
	 * Method that serves as the parser function handler. 
	 * It's responsible for executing all needed logic, and then creating the map output. 
	 * 
	 * @param Parser $parser
	 * @param array $params
	 * 
	 * @return array
	 */
	function getMapHtml( Parser &$parser, array $params );
}