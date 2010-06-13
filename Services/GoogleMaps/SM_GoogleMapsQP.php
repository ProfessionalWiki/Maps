<?php
/**
 * A query printer for maps using the Google Maps API
 *
 * @file SM_GoogleMaps.php
 * @ingroup SMGoogleMaps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMapsQP extends SMMapPrinter {
	
	protected function getServiceName() {
		return 'googlemaps2';
	}
	
	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix, $egMapsGMapOverlays;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;

		$this->defaultZoom = $egMapsGoogleMapsZoom;
		
		$this->specificParameters = array(
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
	 * @see SMMapPrinter::doMapServiceLoad()
	 */
	protected function doMapServiceLoad() {
		global $wgParser, $egGoogleMapsOnThisPage;

		if ( empty( $egGoogleMapsOnThisPage ) ) {
			$egGoogleMapsOnThisPage = 0;
			$this->mService->addDependencies( $wgParser );
		}
		
		$egGoogleMapsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see SMMapPrinter::getQueryResult()
	 */
	protected function addSpecificMapHTML( Parser $parser ) {
		MapsGoogleMaps::addOverlayOutput( $this->output, $parser, $this->mapName, $this->overlays, $this->controls );
		
		// TODO: refactor up like done in maps with display point
		$markerItems = array();
		
		foreach ( $this->mLocations as $location ) {
			list( $lat, $lon, $title, $label, $icon ) = $location;
			$markerItems[] = "getGMarkerData($lat, $lon, '$title', '$label', '$icon')";
		}
		
		// Create a string containing the marker JS.
		$markersString = implode( ',', $markerItems );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$parser->getOutput()->addHeadItem(
			Html::inlineScript( <<<EOT
addOnloadHook(
	function() {
		initializeGoogleMap('$this->mapName', 
			{
				lat: $this->centreLat,
				lon: $this->centreLon,
				zoom: $this->zoom,
				type: $this->type,
				types: [$this->types],
				controls: [$this->controls],
				scrollWheelZoom: $this->autozoom
			},
			[$markersString]	
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
        
        $allowedTypes = MapsGoogleMaps::getTypeNames();
        
        $params[] = array( 'name' => 'controls', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_controls' ), 'values' => MapsGoogleMaps::getControlNames() );
        $params[] = array( 'name' => 'types', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_types' ), 'values' => $allowedTypes );
        $params[] = array( 'name' => 'type', 'type' => 'enumeration', 'description' => wfMsg( 'semanticmaps_paramdesc_type' ), 'values' => $allowedTypes );
        $params[] = array( 'name' => 'overlays', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_overlays' ), 'values' => MapsGoogleMaps::getOverlayNames() );
        $params[] = array( 'name' => 'autozoom', 'type' => 'enumeration', 'description' => wfMsg( 'semanticmaps_paramdesc_autozoom' ), 'values' => array( 'on', 'off' ) );
        
        return $params;
    }
	
}