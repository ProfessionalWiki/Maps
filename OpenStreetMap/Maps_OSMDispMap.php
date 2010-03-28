<?php

/**
 * Class for handling the display_map parser function with OSM.
 *
 * @file Maps_OSMDispMap.php
 * @ingroup MapsOpenStreetMap
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsOSMDispMap extends MapsBaseMap {
	
	public $serviceName = MapsOSM::SERVICE_NAME;
	
	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsOSMZoom, $egMapsOSMPrefix, $egMapsOSMStaticAsDefault, $egMapsOSMStaticActivatable;
		
		$this->elementNamePrefix = $egMapsOSMPrefix;
		$this->defaultZoom = $egMapsOSMZoom;
		
		$modes = MapsOSM::getModeNames();
		
		$this->spesificParameters = array(
			'static' => array(
				'type' => 'boolean',
				'default' => $egMapsOSMStaticAsDefault,
				'output-type' => 'boolean'
				),
			'mode' => array(
				'criteria' => array(
					'in_array' => $modes
					),
				'default' => $modes[0]
				),
			'activatable' => array(
				'type' => 'boolean',
				'default' => $egMapsOSMStaticActivatable,
				'output-type' => 'boolean'
				),
		);
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
				markers: [],
				controls: [$this->controls]
			}
		);
	}
);			
EOT
		) );		
	
		$this->output .= $this->static ? $this->getStaticMap() : $this->getDynamicMap();
	}
	
	/**
	 * Returns html for a dynamic map.
	 * 
	 * @return string
	 */
	private function getDynamicMap() {
		global $wgOut;
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {		
		slippymaps['$this->mapName'].init();
	}
);	
EOT
		);
		
		return Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			wfMsg('maps-loading-map')
		);
	}
	
	/**
	 * Returns html for a static map.
	 * 
	 * @return string
	 */
	private function getStaticMap() {
		$mode = MapsOSM::getModeData( $this->mode );

		$staticType		= $mode['handler'];
		$staticOptions	= $mode['options'];
		
		$static 		= new $staticType( $this->centre_lat, $this->centre_lon, $this->zoom, $this->width, $this->height, $this->lang, $staticOptions );
		$rendering_url 	= $static->getUrl();
		
		$alt = wfMsg( 'maps_centred_on', $this->centre_lat, $this->centre_lon );
		$title = $this->activatable ? wfMsg( 'maps_click_to_activate' ) : $alt;
		
		$image = array(
			'class' => 'mapPreview',
			'src' => $rendering_url,
			'width' => $this->width,
			'height' => $this->height,
			'alt' => $alt,
			'title' => $title,
		);
		
		if ( $this->activatable ) {
			$image['onclick'] = "slippymaps['$this->mapName'].init();";
		}

		return Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			Html::element(
				'img',
				$image
			)
		);
	}
	
}

