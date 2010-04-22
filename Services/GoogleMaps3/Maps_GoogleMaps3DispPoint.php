<?php

/**
 * File holding the MapsGoogleMaps3DispPoint class.
 *
 * @file Maps_GoogleMaps3DispPoint.php
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with Google Maps v3.
 *
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMaps3DispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsGoogleMaps3::SERVICE_NAME;

	protected $markerStringFormat = 'getGMaps3MarkerData(lat, lon, \'title\', \'label\', "icon")';
	
	protected function getDefaultZoom() {
		global $egMapsGMaps3Zoom; 
		return $egMapsGMaps3Zoom;
	}		
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egGMaps3OnThisPage;
		
		MapsGoogleMaps3::addGMap3Dependencies( $this->output );
		$egGMaps3OnThisPage++;
		
		$this->elementNr = $egGMaps3OnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */
	public function addSpecificMapHTML( Parser $parser ) {
		global $egMapsGMaps3Prefix, $egGMaps3OnThisPage;
		
		$mapName = $egMapsGMaps3Prefix . '_' . $egGMaps3OnThisPage;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'width' => $this->width,
				'height' => $this->height
			),
			null
		);
		
		$parser->getOutput()->addHeadItem(
			Html::inlineScript( <<<EOT
addOnloadHook(
	function() {
		initGMap3(
			'$mapName',
			{
				zoom: $this->zoom,
				lat: $this->centreLat,
				lon: $this->centreLon,	
				types: [],
				mapTypeId: $this->type
			},
			[$this->markerString]
		);
	}
);
EOT
			)
		);
	}
	
}

