<?php

/**
 * File holding the MapsOpenLayersDispPoint class.
 *
 * @file Maps_OpenLayersDispPoint.php
 * @ingroup MapsOpenLayers
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with OpenLayers.
 *
 * @author Jeroen De Dauw
 */
class MapsOpenLayersDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsOpenLayers::SERVICE_NAME;
	
	protected $markerStringFormat = 'getOLMarkerData(lon, lat, "title", "label", "icon")';

	protected function getDefaultZoom() {
		global $egMapsOpenLayersZoom;
		return $egMapsOpenLayersZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 */
	public function doMapServiceLoad() {
		global $egOpenLayersOnThisPage;
		
		MapsOpenLayers::addDependencies( $this->parser );
		$egOpenLayersOnThisPage++;
		
		$this->elementNr = $egOpenLayersOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML() {
		global $egMapsOpenLayersPrefix, $egOpenLayersOnThisPage;
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies( $this->output, $this->layers );
		
		$mapName = $egMapsOpenLayersPrefix . '_' . $egOpenLayersOnThisPage;
		
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
		initOpenLayer(
			'$mapName',
			$this->centreLon,
			$this->centreLat,
			$this->zoom,
			[$layerItems],
			[$this->controls],
			[$this->markerString]
		);
	}
);
EOT
		) );
	}

}