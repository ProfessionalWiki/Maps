<?php

/**
 * File holding interface iMappingFeature.
 * 
 * @file iMappingFeature.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Interface that should be implemented by all mapping features that want to use the.
 * 
 * @since 0.6.5
 * 
 * @author Jeroen De Dauw
 */
interface iMappingFeature {
	
	/**
	 * Adds the HTML specific to the mapping service to the output.
	 * 
	 * @since 0.6.5
	 * 
	 * @return string
	 */
	function addSpecificMapHTML();
	
	/**
	 * Returns the specific parameters by first checking if they have been initialized yet,
	 * doing to work if this is not the case, and then returning them.
	 * 
	 * @since 0.6.5
	 * 
	 * @return array
	 */
	function getSpecificParameterInfo();
	
}