<?php
/**
 * A query printer for maps using the Yahoo Maps API
 *
 * @file SM_YahooMaps.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMYahooMaps extends SMMapPrinter {
	// TODO: create a class instead of a code horror :D

	public function getName() {
		wfLoadExtensionMessages('SemanticMaps');
		return wfMsg('sm_yahoomaps_printername');
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
	
	protected function getResultText($res, $outputmode) {
		parent::getResultText($res, $outputmode);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item.
		foreach($this->m_params as $paramName => $paramValue) {
			if (empty(${$paramName})) ${$paramName} = $paramValue;
		}
		
		$result = "";
		
		global $egYahooMapsOnThisPage, $egMapsYahooMapsZoom;
		
		if (strlen($zoom) < 1) $zoom = $egMapsYahooMapsZoom;
		
		if (empty($egYahooMapsOnThisPage)) {
			$egYahooMapsOnThisPage = 0;
			MapsYahooMaps::addYMapDependencies($result);	
		}
		
		$egYahooMapsOnThisPage++;
		
		// Get the Yahoo Maps names for the control and map types
		$type = MapsYahooMaps::getYMapType($type);
		$extraMapControls = self::getExtraMapControls($controls, $egYahooMapsOnThisPage);
		
		$map_text = "";
		
		if (count($this->m_locations) > 0) {
			if (empty($centre)) {
				// If the center is not set, it needs to be determined, together with the bounds
				// This is done with the getBestZoomAndCenter function of the Y! Maps API
				$map_text .= "var ymap_locations_$egYahooMapsOnThisPage = Array();";
				
				foreach ($this->m_locations as $i => $location) {
					// Add the markers to the map
					list($lat, $lon, $title, $label, $icon) = $location;
					$title = str_replace("'", "\'", $title);
					$label = str_replace("'", "\'", $label);
					$map_text .= "
						yahoo_$egYahooMapsOnThisPage.addOverlay(createYMarker(new YGeoPoint($lat, $lon), '$title', '$label'));
						ymap_locations_$egYahooMapsOnThisPage.push(new YGeoPoint($lat, $lon));";
				}
				
				$map_text .= "var centerAndZoom = yahoo_$egYahooMapsOnThisPage.getBestZoomAndCenter(ymap_locations_$egYahooMapsOnThisPage); 
				yahoo_$egYahooMapsOnThisPage.drawZoomAndCenter(centerAndZoom.YGeoPoint, centerAndZoom.zoomLevel);";
			}
			else {
				//if ($centre == null) {
				//	$centre_lat = 0;
				//	$centre_lon = 0;
				//}
				//else {
					// If the center is set, get the coordinates
					list($centre_lat, $centre_lon) = MapsUtils::getLatLon($centre);
				//}
				
				foreach ($this->m_locations as $i => $location) {
					// Add the markers to the map
					list($lat, $lon, $title, $label, $icon) = $location;
					$title = str_replace("'", "\'", $title);
					$label = str_replace("'", "\'", $label);
					$map_text .= "yahoo_$egYahooMapsOnThisPage.addOverlay(createYMarker(new YGeoPoint($lat, $lon), '$title', '$label'));";
				}
				
				$map_text .= "	yahoo_$egYahooMapsOnThisPage.drawZoomAndCenter(new YGeoPoint($centre_lat, $centre_lon), $zoom);";
			}
		}		
		
		// Disbale the scroll wheel zoom when autozoom is set to off
		switch($autozoom) {
			case 'no' : case 'off' : 
				$disbaleKeyControlCode = "yahoo_$egYahooMapsOnThisPage.disableKeyControls();";
				break;
			default:
				$disbaleKeyControlCode = '';
				break;
		}		
		
		$width = $width . 'px';
		$height = $height . 'px';		
		
		$result .= "
		<div id='map-yahoo-$egYahooMapsOnThisPage' style='width: $width; height: $height;'></div>  
		
		<script type='text/javascript'>/*<![CDATA[*/
		var yahoo_locations_$egYahooMapsOnThisPage = new YGeoPoint($lat, $lon);
		var yahoo_$egYahooMapsOnThisPage = new YMap(document.getElementById('map-yahoo-$egYahooMapsOnThisPage'));
		yahoo_$egYahooMapsOnThisPage.addTypeControl(); $extraMapControls 
		yahoo_$egYahooMapsOnThisPage.setMapType($type); 
		$disbaleKeyControlCode $map_text /*]]>*/</script>";
		
		return array($result, 'noparse' => 'true', 'isHTML' => 'true');
	}


}