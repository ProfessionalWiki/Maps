<?php

/**
 * Class for handling the display_map parser hook with OSM.
 * 
 * @since 0.6.4
 * 
 * @file Maps_OSMDispMap.php
 * @ingroup OSM
 * 
 * @author Jeroen De Dauw
 */
class MapsOSMDispMap extends MapsBaseMap {
	
	/**
	 * @see MapsBaseMap::getMapHTML()
	 */
	public function getMapHTML( array $params, Parser $parser ) {	
		global $wgLang;
		
		$thumbs = $this->thumbs ? 'yes' : 'no';
		$photos = $this->photos ? 'yes' : 'no';
		$lang = $wgLang->getCode();
		
		// https://secure.wikimedia.org/wikipedia/de/wiki/Wikipedia:WikiProjekt_Georeferenzierung/Wikipedia-World/en#Expert_mode
		$this->output .= Html::element(
			'iframe',
			array(
				'id' => $this->service->getMapId(),
				'style' => "width: $this->width; height: $this->height; clear: both;",
				'src' => "http://toolserver.org/~kolossos/openlayers/kml-on-ol.php?zoom={$this->zoom}&lat={$this->centreLat}&lon={$this->centreLon}&lang=$lang&thumbs=$thumbs&photo=$photos"
			),
			wfMsg( 'maps-loading-map' )
		);
	}
	
}