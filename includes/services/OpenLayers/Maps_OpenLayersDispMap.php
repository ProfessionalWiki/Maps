<?php

/**
 * Class for handling the display_map parser hook with OpenLayers.
 *
 * @file Maps_OpenLayersDispMap.php
 * @ingroup MapsOpenLayers
 *
 * @author Jeroen De Dauw
 */
class MapsOpenLayersDispMap extends MapsBaseMap {
	
	/**
	 * @see MapsBaseMap::getJSONObject
	 *
	 * @since 0.7.3
	 *
	 * @param array $params
	 * @param Parser $parser
	 * 
	 * @return mixed
	 */	
	protected function getJSONObject( array $params, Parser $parser ) {
		global $wgLang;
		$params['langCode'] = $wgLang->getCode();
		return $params;
	}	
	
	/**
	 * @see MapsBaseMap::getMapHTML
	 * 
	 * @since 0.7.3
	 */
	public function getMapHTML( array $params, Parser $parser ) {
		global $wgLang;

		$mapName = $this->service->getMapId();
		
		if ( !method_exists( 'OutputPage', 'addModules' ) ) {
			$langCode = $wgLang->getCode();
			$centreLat = MapsMapper::encodeJsVar( $params['centre']['lat'] );
			$centreLon = MapsMapper::encodeJsVar( $params['centre']['lon'] );
			$zoom = MapsMapper::encodeJsVar( $params['zoom'] );
			
			MapsMapper::addInlineScript( $this->service, <<<EOT
			initOpenLayer(
				"$mapName",
				$centreLon,
				$centreLat,
				$zoom,
				{$params['layers'][0]},
				[{$params['controls']}],
				[],
				"$langCode"
			);
EOT
			);			
		}

		return Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: {$params['width']}; height: {$params['height']}; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);		
	}

}