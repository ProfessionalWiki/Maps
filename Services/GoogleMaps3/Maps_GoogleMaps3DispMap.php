<?php

/**
 * Class for handling the display_map parser function with Google Maps v3.
 *
 * @file Maps_GoogleMaps3DispMap.php
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_map parser functions with Google Maps v3.
 *
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMaps3DispMap extends MapsBaseMap {
	
	protected function getDefaultZoom() {
		global $egMapsGMaps3Zoom;
		return $egMapsGMaps3Zoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */
	public function doMapServiceLoad() {
		global $egGMaps3OnThisPage;
		
		$egGMaps3OnThisPage++;
		
		$this->elementNr = $egGMaps3OnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */
	public function addSpecificMapHTML() {
		global $egMapsGMaps3Prefix, $egGMaps3OnThisPage;
		
		$mapName = $egMapsGMaps3Prefix . '_' . $egGMaps3OnThisPage;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;"
			),
			null
		);
		
		$this->parser->getOutput()->addHeadItem(
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
			[]
		);
	}
);
EOT
			)
		);
	}
	
}

