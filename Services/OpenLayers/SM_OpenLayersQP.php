<?php

/**
 * A query printer for maps using the Open Layers API
 *
 * @file SM_OpenLayersQP.php 
 * @ingroup SMOpenLayers
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMOpenLayersQP extends SMMapPrinter {

	protected function getServiceName() {
		return 'openlayers';
	}	
	
	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsOpenLayersZoom;
		$this->defaultZoom = $egMapsOpenLayersZoom;
	}
	
	/**
	 * @see SMMapPrinter::addSpecificMapHTML()
	 */
	protected function addSpecificMapHTML() {
		global $wgLang, $egMapsOpenLayersPrefix, $egOpenLayersOnThisPage;
		
		$egOpenLayersOnThisPage++;
		$mapName = $egMapsOpenLayersPrefix . '_' . $egOpenLayersOnThisPage;			
		
		// TODO: refactor up like done in maps with display point
		$markerItems = array();
		
		foreach ( $this->mLocations as $location ) {
			// Create a string containing the marker JS .
			list( $lat, $lon, $title, $label, $icon ) = $location;

			$markerItems[] = "getOLMarkerData($lon, $lat, '$title', '$label', '$icon')";
		}

		$markersString = implode( ',', $markerItems );

		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$layerItems = $this->mService->createLayersStringAndLoadDependencies( $this->layers );
		
		$langCode = $wgLang->getCode();
		
		$this->mService->addDependency( Html::inlineScript( <<<EOT
addOnloadHook(
	function() {
		initOpenLayer(
			'$mapName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			[$layerItems],
			[$this->controls],
			[$markersString],
			'$langCode'
		);
	}
);
EOT
		) );
	}

	/**
	 * Returns type info, descriptions and allowed values for this QP's parameters after adding the specific ones to the list.
	 */
    public function getParameters() {
        $params = parent::getParameters();
        
        $params[] = array( 'name' => 'controls', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_controls' ), 'values' => MapsOpenLayers::getControlNames() );
        $params[] = array( 'name' => 'layers', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_layers' ), 'values' => MapsOpenLayers::getLayerNames() );
        
        return $params;
    }
	
}