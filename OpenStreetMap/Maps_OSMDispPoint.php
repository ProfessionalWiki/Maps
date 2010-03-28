<?php

/**
 * File holding the MapsOSMDispPoint class.
 *
 * @file Maps_OSMDispPoint.php
 * @ingroup MapsOpenStreetMap
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with OSM.
 *
 * @author Jeroen De Dauw
 */
class MapsOSMDispPoint extends MapsBasePointMap {
	
	public $serviceName = MapsOSM::SERVICE_NAME;
	
	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsOSMZoom, $egMapsOSMPrefix;
		
		$this->elementNamePrefix = $egMapsOSMPrefix;
		$this->defaultZoom = $egMapsOSMZoom;
		
		$this->markerStringFormat = 'getOSMMarkerData(lat, lon, \'title\', \'label\', "icon")';
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egOSMMapsOnThisPage;
		
		MapsOSM::addOSMDependencies( $this->output );
		$egOSMMapsOnThisPage++;
		
		$this->elementNr = $egOSMMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */
	public function addSpecificMapHTML( Parser $parser ) {
		$parser->getOutput()->addHeadItem(
			Html::inlineScript( <<<EOT
addOnloadHook(
	function() {		
		slippymaps['$this->mapName'] = new slippymap_map(
			'$this->mapName',
			{
				mode: '$this->mode',
				layer: 'osm-like',
				locale: '$this->lang',
				lat: $this->centre_lat,
				lon: $this->centre_lon,
				zoom: $this->zoom,
				markers: [$this->markerString],
				controls: [$this->controls]
			}
		);
		slippymaps['$this->mapName'].init();
	}
);	
EOT
		) );
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			wfMsg('maps-loading-map')
		);
	}
}