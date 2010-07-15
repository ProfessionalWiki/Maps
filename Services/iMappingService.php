<?php

/**
 * File holding interface iMapParserFunction.
 * 
 * @file iMappingService.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
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
	
	/**
	 * Adds the dependencies to the parser output as head items.
	 * 
	 * @since 0.6.3
	 * 
	 * @param mixed $parserOrOut
	 */
	function addDependencies( &$parserOrOut );
	
	/**
	 * Returns an array that specifies the parameters supported by this service,
	 * together with their meta-data. This is in a format usable by Validator.
	 * 
	 * @since 0.6.3
	 * 
	 * @return array
	 */
	function getParameterInfo();
	
	/**
	 * Returns the default zoomlevel for the mapping service.
	 * 
	 * @since 0.6.5
	 * 
	 * @return integer
	 */
	function getDefaultZoom();
	
	/**
	 * Returns a string that can be used as an unique ID for the map html element.
	 * Increments the number by default, providing false for $increment will get
	 * you the same ID as on the last request.
	 * 
	 * @since 0.6.5
	 * 
	 * @param boolean $increment
	 * 
	 * @return string
	 */
	function getMapId( $increment = true );

	/**
	 * Creates a JS array with marker meta data that can be used to construct a 
	 * map with markers via a function in this services JS file.
	 * 
	 * @since 0.6.5
	 * 
	 * @param array $markers
	 * 
	 * @return string
	 */	
	function createMarkersJs( array $markers );	

}