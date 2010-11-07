<?php

/**
 * Class for handling the display_map parser hook with Google Maps v3.
 *
 * @ingroup MapsGoogleMaps3
 *
 * @author Jeroen De Dauw
 */
final class MapsGoogleMaps3DispMap extends MapsBaseMap {
	
	/**
	 * @see MapsBaseMap::getMapHTML()
	 */
	public function getMapHTML( array $params, Parser $parser ) {
		$mapName = $this->service->getMapId();
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;"
			),
			null
		);
		
		MapsMapper::addInlineScript( $this->service, <<<EOT
		initGMap3(
			"$mapName",
			{
				zoom: $this->zoom,
				lat: $this->centreLat,
				lon: $this->centreLon,	
				types: [],
				mapTypeId: $this->type
			},
			[]
		);
EOT
		);
	}
	
}