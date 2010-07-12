<?php
/**
 * A query printer for maps using the Google Maps API.
 *
 * @file SM_GoogleMapsQP.php
 * @ingroup SMGoogleMaps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class SMGoogleMapsQP extends SMMapPrinter {
	
	/**
	 * @see SMMapPrinter::getServiceName
	 */	
	protected function getServiceName() {
		return 'googlemaps2';
	}
	
	/**
	 * @see SMMapPrinter::initSpecificParamInfo
	 */
	protected function initSpecificParamInfo( array &$parameters ) {
		global $egMapsGMapOverlays;

		$parameters = array(
			'overlays' => array(
				'type' => array( 'string', 'list' ),
				'criteria' => array(
					'is_google_overlay' => array()
				),
				'default' => $egMapsGMapOverlays,
			),
		);
	}
	
	/**
	 * @see SMMapPrinter::addSpecificMapHTML
	 */
	public function addSpecificMapHTML() {
		$mapName = $this->service->getMapId();	
		
		$this->service->addOverlayOutput( $this->output, $mapName, $this->overlays, $this->controls );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$this->service->addDependency( Html::inlineScript( <<<EOT
addOnloadHook(
	function() {
		initializeGoogleMap('$mapName', 
			{
				lat: $this->centreLat,
				lon: $this->centreLon,
				zoom: $this->zoom,
				type: $this->type,
				types: [$this->types],
				controls: [$this->controls],
				scrollWheelZoom: $this->autozoom,
				kml: [$this->kml]
			},
			$this->markerJs	
		);
	}
);
EOT
		) );
	}
	
	/**
	 * Returns type info, descriptions and allowed values for this QP's parameters after adding the
	 * specific ones to the list.
	 * 
	 * @return array
	 */
    public function getParameters() {
        $params = parent::getParameters();
        
        $allowedTypes = MapsGoogleMaps::getTypeNames();
        
        $params[] = array( 'name' => 'controls', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_controls' ), 'values' => MapsGoogleMaps::getControlNames() );
        $params[] = array( 'name' => 'types', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_types' ), 'values' => $allowedTypes );
        $params[] = array( 'name' => 'type', 'type' => 'enumeration', 'description' => wfMsg( 'semanticmaps_paramdesc_type' ), 'values' => $allowedTypes );
        $params[] = array( 'name' => 'overlays', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_overlays' ), 'values' => MapsGoogleMaps::getOverlayNames() );
        $params[] = array( 'name' => 'autozoom', 'type' => 'enumeration', 'description' => wfMsg( 'semanticmaps_paramdesc_autozoom' ), 'values' => array( 'on', 'off' ) );
        
        return $params;
    }
	
}