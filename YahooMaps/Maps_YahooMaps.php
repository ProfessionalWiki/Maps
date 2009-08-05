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

	const SERVICE_NAME = 'yahoomaps';
	
	public $serviceName = self::SERVICE_NAME;		
	
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
	 *
	 * @param string $type
	 * @return string
	 */
	public static function getYMapType($type) {
		global $egMapsYahooMapsType;
		if (! array_key_exists($type, MapsYahooMaps::$mapTypes)) $type = $egMapsYahooMapsType;
		return MapsYahooMaps::$mapTypes[ $type ];
	}
	
	/**
	 * Build up a csv string with the controls, to be outputted as a JS array
	 *
	 * @param array $controls
	 * @return csv string
	 */
	public static function createControlsString(array $controls) {
		global $egMapsYMapControls;
		return MapsMapper::createJSItemsString($controls, $egMapsYMapControls);
	}	

	/**
	 * Retuns an array holding the default parameters and their values.
	 *
	 * @return array
	 */
	public static function getDefaultParams() {
		return array
			(
			'type' => '',
			'autozoom' => '',
			); 		
	}	

	/**
	 * Add references to the Yahoo! Maps API and required JS file to the provided output 
	 *
	 * @param string $output
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
	 * Retuns a boolean as string, true if $autozoom is on or yes.
	 *
	 * @param string $autozoom
	 * @return string
	 */
	public static function getAutozoomJSValue($autozoom) {
		return MapsMapper::getJSBoolValue(in_array($autozoom, array('on', 'yes')));
	}	
	
	/**
	 * @see MapsBaseMap::setFormInputSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom, $egMapsYahooMapsPrefix;
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
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

		$this->autozoom = self::getAutozoomJSValue($this->autozoom);
		
		$markerItems = array();		
		
		// TODO: Refactor up
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];
			$markerItems[] = "getYMarkerData($lat, $lon, '$this->title', '$this->label', '')";
		}		
		
		$markersString = implode(',', $markerItems);			
		
		$this->output .= <<<END
		<div id="$this->mapName" style="width: $this->width; height: $this->height;"></div>  
		
		<script type="$wgJsMimeType">/*<![CDATA[*/
		addLoadEvent(
			initializeYahooMap('$this->mapName', $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, [$this->controls], $this->autozoom, [$markersString])
		);
			/*]]>*/</script>
END;
	}

}
