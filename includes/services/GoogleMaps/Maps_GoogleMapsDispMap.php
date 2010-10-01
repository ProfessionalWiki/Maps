<?php

/**
 * Class for handling the display_map parser functions with Google Maps.
 *
 * @file Maps_GoogleMapsDispMap.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMapsDispMap extends MapsBaseMap {
	
	protected function initSpecificParamInfo( array &$parameters ) {
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML( Parser $parser ) {
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
		
		MapsMapper::addInlineScript( <<<EOT
		initializeGoogleMap("$mapName", 
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
		[]);
EOT
		);
	}
	
}