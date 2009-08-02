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

final class SMMapper extends SMWResultPrinter {
	
	protected function getResultText($res, $outputmode) {
		global $egMapsDefaultService, $egMapsServices;
		
		// TODO: allow service parameter to override the default
		if ($this->mFormat == 'map') $this->mFormat = $egMapsDefaultService;
		
		$service = MapsMapper::getValidService($this->mFormat); 
		
		$queryPrinter = new $egMapsServices[$service]['qp']['class']();
		
		return $queryPrinter->getResultText($res, $outputmode);
	}
	
}