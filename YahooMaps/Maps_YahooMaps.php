<?php
/**
 * A class that holds static helper functions for Yahoo! Maps
 *
 * @file Maps_YahooMaps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

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
	 * Returns the Yahoo Map Control type for the provided a general map control
	 * type. When no match is found, the provided control name will be used.
	 */
	public static function getExtraMapControls($controls, $yahooMapsOnThisPage) {
		global $egMapsYMapControls;
		
		$extraMapControls = '';
		$panAdded = false; $zoomAdded = false;
		
		if (count($controls) < 1) $controls = $egMapsYMapControls; // In case no controls are provided, use the default
		
		foreach ($controls as $control) { // Loop through the controls, and add the JS needed to add them
			switch (strtolower($control)) {
				case 'pan' : 
					if (!$panAdded) {$extraMapControls .= "yahoo_$yahooMapsOnThisPage.addPanControl(); "; $panAdded = true; }
					break;				
				case 'zoom' : 
					if (!$zoomAdded) {$extraMapControls .= "yahoo_$yahooMapsOnThisPage.addZoomLong(); "; $zoomAdded = true; }
					break;
			}
		}
		
		return $extraMapControls;
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
		global $egYahooMapsKey, $egMapsIncludePath;
		$output .= "<script type='$wgJsMimeType' src='http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=$egYahooMapsKey'></script>
		<script type='$wgJsMimeType' src='$egMapsIncludePath/YahooMaps/YahooMapFunctions.js'></script>";
	}

	public static function displayMap(&$parser, $map) {
		global $egYahooMapsOnThisPage, $egMapsYahooMapsZoom;
		
		$params = MapsMapper::setDefaultParValues($map, true);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item.
		foreach($params as $paramName => $paramValue) {
			if (empty(${$paramName})) ${$paramName} = $paramValue;
		}	

		if (strlen($zoom) < 1) $zoom = $egMapsYahooMapsZoom;		
		
		$output = '';

		if (empty($egYahooMapsOnThisPage)) {
			$egYahooMapsOnThisPage = 0;
			MapsYahooMaps::addYMapDependencies($output);	
		}
		
		$egYahooMapsOnThisPage++;
		
		$type = MapsYahooMaps::getYMapType($type);
		$extraMapControls = MapsYahooMaps::getExtraMapControls($controls, $egYahooMapsOnThisPage);

		// Disbale the scroll wheel zoom when autozoom is set to off
		switch($autozoom) {
			case 'no' : case 'off' : 
				$disbaleKeyControlCode = "yahoo_$egYahooMapsOnThisPage.disableKeyControls();";
				break;
			default:
				$disbaleKeyControlCode = '';
				break;
		}
		
		$coordinates = str_replace('″', '"', $coordinates);
		$coordinates = str_replace('′', "'", $coordinates);

		list($lat, $lon) = MapsUtils::getLatLon($coordinates);

		$width = $width . 'px';
		$height = $height . 'px';

		$output .=<<<END
		<div id="map-yahoo-$egYahooMapsOnThisPage" style="width: $width; height: $height;"></div>  
		
		<script type="text/javascript">/*<![CDATA[*/
		var yahoo_location_$egYahooMapsOnThisPage = new YGeoPoint($lat, $lon); 
		var yahoo_$egYahooMapsOnThisPage = new YMap(document.getElementById('map-yahoo-$egYahooMapsOnThisPage'));  
		yahoo_$egYahooMapsOnThisPage.addTypeControl();  
		$extraMapControls  yahoo_$egYahooMapsOnThisPage.setMapType($type); $disbaleKeyControlCode
		yahoo_$egYahooMapsOnThisPage.addOverlay(createYMarker(yahoo_location_$egYahooMapsOnThisPage, '', '')); 
		yahoo_$egYahooMapsOnThisPage.drawZoomAndCenter(yahoo_location_$egYahooMapsOnThisPage, $zoom); /*]]>*/</script>
END;

		return $output;
	}

}
