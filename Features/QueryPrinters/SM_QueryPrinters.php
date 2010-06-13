<?php

/**
 * Initialization file for query printer functionality in the Semantic Maps extension
 *
 * @file SM_QueryPrinters.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['SMQueryPrinters'] = __FILE__;

$wgHooks['MappingFeatureLoad'][] = 'SMQueryPrinters::initialize';

final class SMQueryPrinters {
	
	public static $parameters = array();
	
	/**
	 * Initialization function for Maps query printer functionality.
	 */
	public static function initialize() {
		global $smgDir, $wgAutoloadClasses, $egMapsServices;
		
		$wgAutoloadClasses['SMMapper'] 	= dirname( __FILE__ ) . '/SM_Mapper.php';
		$wgAutoloadClasses['SMMapPrinter'] 	= dirname( __FILE__ ) . '/SM_MapPrinter.php';
		
		self::initializeParams();
		
		$hasQueryPrinters = false;

		foreach ( $egMapsServices as $service ) {
			// Check if the service has a query printer.
			$QPClass = $service->getFeature( 'qp' );
			
			// If the service has no QP, skipt it and continue with the next one.
			if ( $QPClass === false ) continue;
			
			// At least one query printer will be enabled when this point is reached.
			$hasQueryPrinters = true;
			
			// Initiate the format.
			self::initFormat( $service->getName(), $QPClass, $service->getAliases() );
		}

		// Add the 'map' result format if there are mapping services that have QP's loaded.
		if ( $hasQueryPrinters ) self::initFormat( 'map', 'SMMapper' );
		
		return true;
	}
	
	private static function initializeParams() {
		global $egMapsDefaultServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsMapWidth, $egMapsMapHeight;
		global $smgQPForceShow, $smgQPShowTitle, $smgQPTemplate;

		self::$parameters = array(
			'width' => array(
				'default' => $egMapsMapWidth
			),
			'height' => array(
				'default' => $egMapsMapHeight
			),
			'geoservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
			'format' => array(
				'required' => true,
				'default' => $egMapsDefaultServices['qp']
			),
			'centre' => array(
				'aliases' => array( 'center' ),
			),
			'forceshow' => array(
				'type' => 'boolean',
				'aliases' => array( 'force show' ),
				'default' => $smgQPForceShow,
				'output-type' => 'boolean'
			),
			'template' => array(
				'criteria' => array(
					'not_empty' => array()
				),
				'default' => $smgQPTemplate,
			),
			'showtitle' => array(
				'type' => 'boolean',
				'aliases' => array( 'show title' ),
				'default' => $smgQPShowTitle,
				'output-type' => 'boolean'
			),
			'icon' => array(
				'criteria' => array(
					'not_empty' => array()
				)
			),
			// SMW #Ask: parameters
			'limit' => array(
				'type' => 'integer',
				'criteria' => array(
					'in_range' => array( 0 )
				)
			),
			'offset' => array(
				'type' => 'integer'
			),
			'sort' => array(),
			'order' => array(
				'criteria' => array(
					'in_array' => array( 'ascending', 'asc', 'descending', 'desc', 'reverse' )
				)
			),
			'headers' => array(
				'criteria' => array(
					'in_array' => array( 'show', 'hide' )
				)
			),
			'mainlabel' => array(),
			'link' => array(
				'criteria' => array(
					'in_array' => array( 'none', 'subject', 'all' )
				)
			),
			'default' => array(),
			'intro' => array(),
			'outro' => array(),
			'searchlabel' => array(),
			'distance' => array(),
		);
	}
	
	/**
	 * Add the result format for a mapping service or alias.
	 *
	 * @param string $format
	 * @param string $formatClass
	 * @param array $aliases
	 */
	private static function initFormat( $format, $formatClass, array $aliases = array() ) {
		global $wgAutoloadClasses, $smgDir, $smwgResultAliases;

		// Add the QP to SMW.
		self::addFormatQP( $format, $formatClass );

		// If SMW supports aliasing, add the aliases to $smwgResultAliases.
		if ( isset( $smwgResultAliases ) ) {
			$smwgResultAliases[$format] = $aliases;
		}
		else { // If SMW does not support aliasing, add every alias as a format.
			foreach ( $aliases as $alias ) self::addFormatQP( $alias, $formatClass );
		}
	}

	/**
	 * Adds a QP to SMW's $smwgResultFormats array or SMWQueryProcessor
	 * depending on if SMW supports $smwgResultFormats.
	 * 
	 * @param string $format
	 * @param string $class
	 */
	private static function addFormatQP( $format, $class ) {
		global $smwgResultFormats;
		
		if ( isset( $smwgResultFormats ) ) {
			$smwgResultFormats[$format] = $class;
		}
		else {
			SMWQueryProcessor::$formats[$format] = $class;
		}
	}
	
}