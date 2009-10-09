<?php

/**
 * Initialization file for query printer functionality in the Semantic Maps extension
 *
 * @file SM_QueryPrinters.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMQueryPrinters {
	
	public static function initialize() {
		global $smgIP, $wgAutoloadClasses, $egMapsServices;
		
		$wgAutoloadClasses['SMMapPrinter'] 	= $smgIP . '/QueryPrinters/SM_MapPrinter.php';
		
		global $egMapsServices;
		
		$hasQueryPrinters = false;
		
		foreach($egMapsServices as $serviceName => $serviceData) {
			// Check if the service has a query printer
			$hasQP = array_key_exists('qp', $serviceData);
			
			// If the service has no QP, skipt it and continue with the next one.
			if (!$hasQP) continue;
			
			// At least one query printer will be enabled when this point is reached.
			$hasQueryPrinters = true;				
			
			// Initiate the format.
			self::initFormat($serviceName, $serviceData['qp'], $serviceData['aliases']);
		}	

		// Add the 'map' result format if there are mapping services that have QP's loaded.
		if ($hasQueryPrinters) self::initFormat('map', array('class' => 'SMMapper', 'file' => 'QueryPrinters/SM_Mapper.php', 'local' => true), array());
	}
	
	/**
	 * Add the result format for a mapping service or alias
	 *
	 * @param string $format
	 * @param array $qp
	 * @param array $aliases
	 */
	private static function initFormat($format, array $qp, array $aliases) {
		global $wgAutoloadClasses, $smwgResultFormats, $smgIP;
		
		if (! array_key_exists($qp['class'], $wgAutoloadClasses)) {
			$file = $qp['local'] ? $smgIP . '/' . $qp['file'] : $qp['file'];
			$wgAutoloadClasses[$qp['class']] = $file;
		}
		
		if (isset($smwgResultFormats)) {
			$smwgResultFormats[$format] = $qp['class'];
		}
		else {
			SMWQueryProcessor::$formats[$format] = $qp['class'];
		}	
		
		// TODO: set aliases
		// PHP 5.3 required
		//call_user_func_array(array($qp['class'], 'setAliases'), array($aliases));
	}	
	
}