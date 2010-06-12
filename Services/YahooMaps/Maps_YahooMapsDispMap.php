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
	
	protected function getDefaultZoom() {
		global $egMapsYahooMapsZoom;
		return $egMapsYahooMapsZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 */
	public function doMapServiceLoad() {
		global $egYahooMapsOnThisPage;
		
		$this->mService->addDependencies( $this->parser );
		$egYahooMapsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML() {
		global $egMapsYahooMapsPrefix, $egYahooMapsOnThisPage;
		
		$mapName = $egMapsYahooMapsPrefix . '_' . $egYahooMapsOnThisPage;
		
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
			'$mapName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
			[]
		);
	}
);
EOT
		) );
	}

}
