<?php

/**
 * File holding the MapsYahooMapsDispPoint class.
 *
 * @file Maps_YahooMapsDispPoint.php
 * @ingroup MapsYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with Yahoo! Maps.
 *
 * @author Jeroen De Dauw
 */
class MapsYahooMapsDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsYahooMaps::SERVICE_NAME;
	
	protected $markerStringFormat = 'getYMarkerData(lat, lon, \'title\', \'label\', "icon")';
	
	protected function getDefaultZoom() {
		global $egMapsYahooMapsZoom;
		return $egMapsYahooMapsZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */
	public function doMapServiceLoad() {
		global $egYahooMapsOnThisPage;
		
		MapsYahooMaps::addYMapDependencies( $this->output );
		$egYahooMapsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */
	public function addSpecificMapHTML( Parser $parser ) {
		global $egMapsYahooMapsPrefix, $egYahooMapsOnThisPage;
		
		$mapName = $egMapsYahooMapsPrefix . '_' . $egYahooMapsOnThisPage;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$parser->getOutput()->addHeadItem(
			Html::inlineScript( <<<EOT
addOnloadHook(
	function() {
		initializeYahooMap(
			'$mapName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
			[$this->markerString]
		);
	}
);
EOT
		) );
	}

}
