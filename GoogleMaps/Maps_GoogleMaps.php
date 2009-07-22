<?php
/**
* Form input hook that adds an Google Maps map format to Semantic Forms
 *
 * @file Maps_GoogleMaps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGoogleMaps extends MapsBaseMap {

	// http://code.google.com/apis/maps/documentation/introduction.html#MapTypes
	private static $mapTypes = array(
					'earth' => 'G_SATELLITE_3D_MAP',
					'normal' => 'G_NORMAL_MAP',
					'satellite' => 'G_SATELLITE_MAP',
					'hybrid' => 'G_HYBRID_MAP',
					'physical' => 'G_PHYSICAL_MAP',
					);

	// http://code.google.com/apis/maps/documentation/controls.html#Controls_overview
	private static $controlClasses = array(
					'large' => 'GLargeMapControl3D',
					'small' => 'GSmallZoomControl3D',
					);
	
	/**
	 * Returns the Google Map type (defined in MapsGoogleMaps::$mapTypes) 
	 * for the provided a general map type. When no match is found, the first 
	 * possible Google Map type will be returned as default.
	 */
	public static function getGMapType($type, $earthEnabled = false) {
		$keyz = array_keys(MapsGoogleMaps::$mapTypes);
		$defaultKey = $earthEnabled ? 0 : 1;
		$keyName = array_key_exists($type, MapsGoogleMaps::$mapTypes) ? $type : $keyz[ "$defaultKey" ];
		return MapsGoogleMaps::$mapTypes[ $keyName ];
	}
	
	/**
	 * Returns the Google Map Control type (defined in MapsGoogleMaps::$controlClasses) 
	 * for the provided a general map control type. When no match is found, the provided
	 * control name will be used.
	 */
	public static function getGControlType($controls) {
		global $egMapsGMapControl;
		$control = count($controls) > 0 ? $controls[0] : $egMapsGMapControl;
		return array_key_exists($control, MapsGoogleMaps::$controlClasses) ? MapsGoogleMaps::$controlClasses[$control] : $control; 
	}
	
	/**
	 * Add references to the Google Maps API and required JS file to the provided output 
	 *
	 * @param unknown_type $output
	 */
	public static function addGMapDependencies(&$output) {
		global $wgJsMimeType, $wgLang;
		global $egGoogleMapsKey, $egMapsIncludePath;
		$output .= "<script src='http://maps.google.com/maps?file=api&v=2&key=$egGoogleMapsKey&hl={$wgLang->getCode()}' type='$wgJsMimeType'></script>
			<script type='$wgJsMimeType' src='$egMapsIncludePath/GoogleMaps/GoogleMapFunctions.js'></script>";
	}

	/**
	 * Build up the HTML for a Google Map with the provided properties and return it.
	 */
	public static function displayMap(&$parser, $map) {
		global $egGoogleMapsKey, $egGoogleMapsOnThisPage, $egMapsGoogleMapsZoom;
		global $wgLang, $wgJsMimeType;

		$params = MapsMapper::setDefaultParValues($map, true);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item.
		foreach($params as $paramName => $paramValue) {
			if (empty(${$paramName})) ${$paramName} = $paramValue;
		}
		
		if (strlen($zoom) < 1) $zoom = $egMapsGoogleMapsZoom;
		
		$output = '';
		
		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies($output);
		}
		$egGoogleMapsOnThisPage++;
		
		switch($earth) {
			case 'on' : case 'yes' :
				$earthCode = "map.addMapType(G_SATELLITE_3D_MAP);";
				break;
			default : 
				$earthCode = '';
				break;
		}			
		
		$type = MapsGoogleMaps::getGMapType($type, strlen($earthCode) > 0);
		$control = MapsGoogleMaps::getGControlType($controls);
		
		// Enable the scroll wheel zoom when autozoom is not set to off
		switch($autozoom) {
			case 'no' : case 'off' : 
				$autozoomCode = '';
				break;
			default:
				$autozoomCode = 'map.enableScrollWheelZoom();';
				break;
		}	
		
		$coordinates = str_replace('″', '"', $coordinates);
		$coordinates = str_replace('′', "'", $coordinates);

		list($lat, $lon) = MapsUtils::getLatLon($coordinates);
		
		$output .=<<<END

<div id="map-google-$egGoogleMapsOnThisPage" class="$class" style="$style" ></div>
<script type="text/javascript">
/*<![CDATA[*/
addLoadEvent(
	function() {
		if (GBrowserIsCompatible()) {
			var map = new GMap2(document.getElementById("map-google-{$egGoogleMapsOnThisPage}"), {size: new GSize('$width', '$height')});
			$autozoomCode $earthCode map.addControl(new {$control}());
			map.addControl(new GMapTypeControl());
			var point = new GLatLng({$lat}, {$lon});
			map.setCenter(point, {$zoom}, {$type});
			var marker = new GMarker(point);
			map.addOverlay(marker);
		}
	}
);
/*]]>*/
</script>

END;

	return $output;
	}


}

