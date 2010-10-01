<?php

/**
 * Class for handling the display_map parser function with OpenLayers.
 *
 * @file Maps_OpenLayersDispMap.php
 * @ingroup MapsOpenLayers
 *
 * @author Jeroen De Dauw
 */
class MapsOpenLayersDispMap extends MapsBaseMap {
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML( Parser $parser ) {
		global $wgLang;

		$layerItems = $this->service->createLayersStringAndLoadDependencies( $this->layers );

		$mapName = $this->service->getMapId();
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$langCode = $wgLang->getCode();
		
		MapsMapper::addInlineScript( $parser, <<<EOT
		initOpenLayer(
			"$mapName",
			$this->centreLon,
			$this->centreLat,
			$this->zoom,
			[$layerItems],
			[$this->controls],
			[],
			"$langCode"
		);
EOT
		);
	}

}