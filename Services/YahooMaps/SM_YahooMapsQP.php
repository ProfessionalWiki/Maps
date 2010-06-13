<?php
/**
 * A query printer for maps using the Yahoo Maps API
 *
 * @file SM_YahooMaps.php
 * @ingroup SMYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMYahooMapsQP extends SMMapPrinter {

	/**
	 * @see SMMapPrinter::setQueryPrinterSettings()
	 */
	protected function setQueryPrinterSettings() {
		global $egMapsYahooMapsZoom, $egMapsYahooMapsPrefix;
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
		
		$this->defaultZoom = $egMapsYahooMapsZoom;
	}
	
	/**
	 * @see SMMapPrinter::doMapServiceLoad()
	 */
	protected function doMapServiceLoad() {
		global $egYahooMapsOnThisPage, $wgParser;
		
		MapsYahooMaps::addYMapDependencies( $wgParser );
		$egYahooMapsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;
	}
	
	/**
	 * @see SMMapPrinter::addSpecificMapHTML()
	 */
	protected function addSpecificMapHTML( Parser $parser ) {
		// TODO: refactor up like done in maps with display point
		$markerItems = array();
		
		foreach ( $this->mLocations as $location ) {
			// Create a string containing the marker JS.
			list( $lat, $lon, $title, $label, $icon ) = $location;
			
			$markerItems[] = "getYMarkerData($lat, $lon, '$title', '$label', '$icon')";
		}
		
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
		initializeYahooMap(
			'$this->mapName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
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
        
        $allowedTypes = MapsYahooMaps::getTypeNames();
        
        $params[] = array( 'name' => 'controls', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_controls' ), 'values' => MapsYahooMaps::getControlNames() );
        $params[] = array( 'name' => 'types', 'type' => 'enum-list', 'description' => wfMsg( 'semanticmaps_paramdesc_types' ), 'values' => $allowedTypes );
        $params[] = array( 'name' => 'type', 'type' => 'enumeration', 'description' => wfMsg( 'semanticmaps_paramdesc_type' ), 'values' => $allowedTypes );
        $params[] = array( 'name' => 'autozoom', 'type' => 'enumeration', 'description' => wfMsg( 'semanticmaps_paramdesc_autozoom' ), 'values' => array( 'on', 'off' ) );
        
        return $params;
    }
	
}