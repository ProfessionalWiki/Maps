<?php

/**
 * Initialization file for query printer functionality in the Semantic Maps extension
 *
 * @file SM_QueryPrinters.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgHooks['MappingFeatureLoad'][] = 'SMQueryPrinters::initialize';

final class SMQueryPrinters {
	
	/**
	 * Initialization function for Maps query printer functionality.
	 */
	public static function initialize() {
		global $wgAutoloadClasses;
		
		$wgAutoloadClasses['SMQueryHandler']	= dirname( __FILE__ ) . '/SM_QueryHandler.php';
		$wgAutoloadClasses['SMMapper'] 			= dirname( __FILE__ ) . '/SM_Mapper.php';
		$wgAutoloadClasses['SMMapPrinter'] 		= dirname( __FILE__ ) . '/SM_MapPrinter.php';
		$wgAutoloadClasses['SMKMLPrinter'] 		= dirname( __FILE__ ) . '/SM_KMLPrinter.php';
		
		self::initFormat( 'kml', 'SMKMLPrinter' );
		
		$hasQueryPrinters = false;

		foreach ( MapsMappingServices::getServiceIdentifiers() as $serviceIdentifier ) {
			$service = MapsMappingServices::getServiceInstance( $serviceIdentifier );	
				
			// Check if the service has a query printer.
			$QPClass = $service->getFeature( 'qp' );
			
			// If the service has no QP, skipt it and continue with the next one.
			if ( $QPClass === false ) continue;
			
			// At least one query printer will be enabled when this point is reached.
			$hasQueryPrinters = true;
			
			// Initiate the format.
			$aliases = $service->getAliases();
			self::initFormat( $service->getName(), 'SMMapper' /* $QPClass */, $aliases );
		}

		// Add the 'map' result format if there are mapping services that have QP's loaded.
		if ( $hasQueryPrinters ) {
			self::initFormat( 'map', 'SMMapper' );
		}
		
		return true;
	}
	
	/**
	 * Add the result format for a mapping service or alias.
	 *
	 * @param string $format
	 * @param string $formatClass
	 * @param array $aliases
	 */
	private static function initFormat( $format, $formatClass, array $aliases = array() ) {
		global $smwgResultAliases;

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
		$smwgResultFormats[$format] = $class;
	}
	
}
