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
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML
	 */
	public function addSpecificMapHTML() {
		$mapName = $this->service->getMapId();
		
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
		initializeYahooMap(
			"$mapName",
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
			$this->markerJs
		);
	}
);
EOT
		) );
	}

}