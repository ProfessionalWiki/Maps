<?php

/**
 * Interface that should be implemented by all map display parser functions.
 * 
* @since 0.6.3
 * 
 * @file iMappingParserFunction.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
interface iMappingParserFunction extends iMappingFeature {
	
	/**
	 * Constructor.
	 * 
	 * @param iMappingService $service
	 */
	function __construct( iMappingService $service );
	
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