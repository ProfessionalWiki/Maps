<?php
/**
 * A class that holds static helper functions for Yahoo! Maps
 *
 * @file Maps_YahooMaps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsYahooMaps extends MapsBaseMap {

	// http://developer.yahoo.com/maps/ajax
	private static $mapTypes = array(
					'normal' => 'YAHOO_MAP_REG',
					'satellite' => 'YAHOO_MAP_SAT',
					'hybrid' => 'YAHOO_MAP_HYB'
					);				
	
	/**
	 * Returns the Yahoo Map type (defined in MapsYahooMaps::$mapTypes) 
	 * for the provided a general map type. When no match is found, the first 
	 * Google Map type will be returned as default.
	 */
	public static function getYMapType($type) {
		$keyz = array_keys(MapsYahooMaps::$mapTypes);
		$keyName = array_key_exists($type, MapsYahooMaps::$mapTypes) ? $type : $keyz[0];
		return MapsYahooMaps::$mapTypes[ $keyName ];
	}
	
	/**
	 * Build up a csv string with the controls, to be outputted as a JS array
	 *
	 * @param unknown_type $controls
	 * @return csv string
	 */
	public static function createControlsString($controls) {
		global $egMapsYMapControls;
		return MapsMapper::createControlsString($controls, $egMapsYMapControls);
	}		

	/**
	 * Add references to the Yahoo! Maps API and required JS file to the provided output 
	 *
	 * @param unknown_type $output
	 */
	public static function addYMapDependencies(&$output) {
		global $wgJsMimeType;
		global $egYahooMapsKey, $egMapsIncludePath, $egYahooMapsOnThisPage;
		
		if (empty($egYahooMapsOnThisPage)) {
			$egYahooMapsOnThisPage = 0;
			$output .= "<script type='$wgJsMimeType' src='http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=$egYahooMapsKey'></script>
			<script type='$wgJsMimeType' src='$egMapsIncludePath/YahooMaps/YahooMapFunctions.js'></script>";
		}
	}

	/**
	 * @see MapsBaseMap::setFormInputSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom;
		
		$this->elementNamePrefix = 'map_yahoo';
		$this->defaultZoom = $egMapsYahooMapsZoom;
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egYahooMapsOnThisPage;
		
		self::addYMapDependencies($this->output);	
		$egYahooMapsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;
	}	
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */		
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$this->type = self::getYMapType($this->type);
		$this->controls = self::createControlsString($this->controls);
		
		MapsUtils::makePxValue($this->width);
		MapsUtils::makePxValue($this->height);

		$this->output .= <<<END
		<div id="$this->mapName" style="width: $this->width; height: $this->height;"></div>  
		
		<script type="$wgJsMimeType">/*<![CDATA[*/
		addLoadEvent(
			initializeYahooMap('$this->mapName', $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$this->controls], $this->autozoom, [getYMarkerData($this->marker_lat, $this->marker_lon, '$this->title', '$this->label', '')])
		);
			/*]]>*/</script>
END;
	}

}
