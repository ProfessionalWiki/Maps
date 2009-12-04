<?php

/**
 * Class for handling the display_map parser function with OSM.
 *
 * @file Maps_OSMDispMap.php
 * @ingroup MapsOpenStreetMap
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsOSMDispMap extends MapsBaseMap {
	
	public $serviceName = MapsOSM::SERVICE_NAME;	
	
	/**
	 * @see MapsBaseMap::setMapSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsOSMZoom, $egMapsOSMPrefix;
		
		$this->elementNamePrefix = $egMapsOSMPrefix;
		$this->defaultZoom = $egMapsOSMZoom;
		
		$modes = MapsOSM::getModeNames();
		
		$this->spesificParameters = array(
			'static' => array(
				'aliases' => array(),
				'criteria' => array(
					'in_array' => array('yes', 'no')
					),
				'default' => 'no'												
				),
			'mode' => array(
				'aliases' => array(),
				'criteria' => array(
					'in_array' => $modes
					),
				'default' => $modes[0]								
				),							
		);		
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egOSMMapsOnThisPage;
		
		MapsOSM::addOSMDependencies($this->output);
		$egOSMMapsOnThisPage++;
		
		$this->elementNr = $egOSMMapsOnThisPage;
	}	
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType;

		$controlItems = MapsMapper::createJSItemsString(explode(',', $this->controls));
		
		$this->output .= <<<EOT
			<script type='$wgJsMimeType'>slippymaps['$this->mapName'] = new slippymap_map('$this->mapName', {
				mode: '$this->mode',
				layer: 'osm-like',
				locale: '$this->lang',
				lat: $this->centre_lat,
				lon: $this->centre_lon,
				zoom: $this->zoom,
				width: $this->width,
				height: $this->height,
				markers: [],
				controls: [$controlItems]
			});</script>

EOT;
	
		$this->output .= $this->static == 'yes' ? $this->getStaticMap() : $this->getDynamicMap();
		
	}
	
	/**
	 * Returns html for a dynamic map.
	 * 
	 * @return string
	 */
	private function getDynamicMap() {
		global $wgJsMimeType;
		
		return <<<EOT
				<!-- map div -->
				<div id='$this->mapName' class='map' style='width:{$this->width}px; height:{$this->height}px;'>
					<script type='$wgJsMimeType'>slippymaps['$this->mapName'].init();</script>
				<!-- /map div -->
				</div>
EOT;
	}
	
	/**
	 * Returns html for a static map.
	 * 
	 * @return string
	 */	
	private function getStaticMap() {
		$clickToActivate = wfMsg('maps_click_to_activate');
		
		$mode = MapsOSM::getModeData($this->mode);

		$staticType				= $mode['static_rendering']['type'];
		$staticOptions			= $mode['static_rendering']['options'];
		
		$static = new $staticType($this->centre_lat, $this->centre_lon, $this->zoom, $this->width, $this->height, $this->lang, $staticOptions);
		$rendering_url = $static->getUrl();
		
		return <<<EOT
				<!-- map div -->
				<div id="$this->mapName" class="map" style="width:{$this->width}px; height:{$this->height}px;">
					<!-- Static preview -->
					<img
						id="$this->mapName-preview"
						class="mapPreview"
						src="{$rendering_url}"
						onclick="slippymaps['$this->mapName'].init();"
						width="$this->width"
						height="$this->height"
						alt="Map centred on $this->centre_lat, $this->centre_lon."
						title="$clickToActivate"/>
				<!-- /map div -->
				</div>
EOT;
	}
	
}

