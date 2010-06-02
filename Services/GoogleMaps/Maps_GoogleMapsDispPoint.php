<?php

/**
 * File holding the MapsGoogleMapsDispPoint class.
 *
 * @file Maps_GoogleMapsDispPoint.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with Google Maps.
 *
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMapsDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsGoogleMaps::SERVICE_NAME;

	protected $markerStringFormat = 'getGMarkerData(lat, lon, "title", "label", "icon")';
	
	protected function getDefaultZoom() {
		global $egMapsGoogleMapsZoom;
		return $egMapsGoogleMapsZoom;
	}
	
	public function getSpecificParameterInfo() {
		global $egMapsGMapOverlays;
		// TODO: it'd be cool to have this static so it can be cheched in order to only init it once.
		$this->spesificParameters = array(
			'overlays' => array(
				'type' => array( 'string', 'list' ),
				'criteria' => array(
					'is_google_overlay' => array()
				),
				'default' => $egMapsGMapOverlays,
			),
		);
		return $this->spesificParameters;
	}

	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 */
	public function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;
		
		MapsGoogleMaps::addGMapDependencies( $this->parser );
		$egGoogleMapsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML() {
		global $egMapsGoogleMapsPrefix, $egGoogleMapsOnThisPage;
		
		$mapName = $egMapsGoogleMapsPrefix . '_' . $egGoogleMapsOnThisPage;
		
		MapsGoogleMaps::addOverlayOutput( $this->output, $this->parser, $mapName, $this->overlays, $this->controls );

		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$this->parser->getOutput()->addHeadItem(
			Html::inlineScript(
				<<<EOT
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
			scrollWheelZoom: $this->autozoom
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

