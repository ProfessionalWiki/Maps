<?php

/**
 * Class for handling the display_map parser function with Yahoo! Maps
 *
 * @file Maps_YahooMapsDispMap.php
 * @ingroup MapsYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsYahooMapsDispMap extends MapsBaseMap {
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML( Parser $parser ) {
		$mapName = $this->service->getMapId();
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		MapsMapper::addInlineScript( <<<EOT
		initializeYahooMap(
			"$mapName",
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
			[]
		);
EOT
		);
	}

}