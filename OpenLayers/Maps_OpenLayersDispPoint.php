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
	
	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsOpenLayersZoom, $egMapsOpenLayersPrefix;
		
		$this->elementNamePrefix = $egMapsOpenLayersPrefix;
		$this->defaultZoom = $egMapsOpenLayersZoom;
		
		$this->markerStringFormat = 'getOLMarkerData(lon, lat, \'title\', \'label\', "icon")';
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egOpenLayersOnThisPage;
		
		MapsOpenLayers::addOLDependencies( $this->output );
		$egOpenLayersOnThisPage++;
		
		$this->elementNr = $egOpenLayersOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */
	public function addSpecificMapHTML() {
		global $wgOut;
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies( $this->output, $this->layers );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'width' => $this->width,
				'height' => $this->height
			),
			null
		);
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		initOpenLayer(
			'$this->mapName',
			$this->centre_lon,
			$this->centre_lat,
			$this->zoom,
			[$layerItems],
			[$this->controls],
			[$this->markerString]
		);
	}
);
EOT
		);
	}

}