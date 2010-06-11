<?php

/**
 * File holding interface iMapParserFunction.
 * 
 * @file Maps_iMappingService.php
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
interface iMappingService {
	
	static function initialize();
	
	static function addDependencies( &$parserOrOut );
	
}