<?php

/**
 * General map query printer class
 *
 * @file SM_Mapper.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMMapper extends SMMapPrinter {
	
	protected function getResultText($res, $outputmode) {
		global $egMapsDefaultService;
		
		// TODO: allow service parameter to override the default
		
		switch ($egMapsDefaultService) {
			case 'openlayers' : case 'layers' : 
				$output = SMOpenLayers::getResultText($res, $outputmode);
				break;
			case 'yahoomaps' : case 'yahoo' : 
				$output = SMYahooMaps::getResultText($res, $outputmode);
				break;	
			default:
				$output = SMGoogleMaps::getResultText($res, $outputmode);
				break;
		}
		
		return $output;
	}
	
}