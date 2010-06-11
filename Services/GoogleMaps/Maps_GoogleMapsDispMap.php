<?php

/**
 * Class for handling the display_map parser function with Google Maps
 *
 * @file Maps_GoogleMapsDispMap.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_map parser functions with Google Maps.
 *
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMapsDispMap extends MapsBaseMap {
	
	public $serviceName = MapsGoogleMaps::SERVICE_NAME;
	
	protected function getDefaultZoom() {
		global $egMapsGoogleMapsZoom;
		return $egMapsGoogleMapsZoom;
	}
	
	public function getSpecificParameterInfo() {
		global $egMapsGMapOverlays;
		$this->specificParameters = array(
			'overlays' => array(
				'type' => array( 'string', 'list' ),
				'criteria' => array(
					'is_google_overlay' => array()
				),
				'default' => $egMapsGMapOverlays,
			),
		);
		return $this->specificParameters;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 */
	public function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;
		
		MapsGoogleMaps::addDependencies( $this->parser );
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
			Html::inlineScript( <<<EOT
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
		[]);
	}
);
EOT
		) );
		
	}
	
}

