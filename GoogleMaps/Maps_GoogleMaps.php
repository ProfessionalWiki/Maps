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
					'normal' => 'G_NORMAL_MAP',
					'satellite' => 'G_SATELLITE_MAP',
					'hybrid' => 'G_HYBRID_MAP',
					'physical' => 'G_PHYSICAL_MAP',
					'earth' => 'G_SATELLITE_3D_MAP',	
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
		global $egMapsGoogleMapsType;
		
		if (! array_key_exists($type, MapsGoogleMaps::$mapTypes)) {
			$type = $earthEnabled ? "earth" : $egMapsGoogleMapsType;;
		}
		
		return self::$mapTypes[ $type ];
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
		global $egGoogleMapsKey, $egMapsIncludePath, $egGoogleMapsOnThisPage;
		
		if (empty($egGoogleMapsOnThisPage)) {
			
			$egGoogleMapsOnThisPage = 0;
			$output .= "<script src='http://maps.google.com/maps?file=api&v=2&key=$egGoogleMapsKey&hl={$wgLang->getCode()}' type='$wgJsMimeType'></script>
			<script type='$wgJsMimeType' src='$egMapsIncludePath/GoogleMaps/GoogleMapFunctions.js'></script>";
		}
	}
	
	/**
	 * Returns a boolean representing if the earth map type should be showed or not,
	 * when provided the the wiki code value.
	 *
	 * @param string $earthValue
	 * @return boolean Indicates wether the earth type should be enabled.
	 */
	public static function getEarthValue($earthValue) {
		$trueValues = array('on', 'yes');
		return in_array($earthValue, $trueValues);		
	}
	
	/**
	 * Returns the JS version (true/false as string) of the provided earth parameter.
	 *
	 * @param boolean $enableEarth
	 * @return string
	 */
	public static function getJSEarthValue($enableEarth) {		
		return $enableEarth ? 'true' : 'false';
	}	

	/**
	 * @see MapsBaseMap::setFormInputSettings()
	 *
	 */	
	protected function setMapSettings() {
		global $egMapsGoogleMapsZoom, $egMapsGoogleMapsPrefix;
		
		$this->elementNamePrefix = $egMapsGoogleMapsPrefix;
		$this->defaultZoom = $egMapsGoogleMapsZoom;
		
		$this->serviceName = 'googlemaps';
		
		$this->defaultParams = array
			(
			'type' => '',
			'class' => 'pmap',
			'autozoom' => '',
			'earth' => ''
			); 
	}
	
	/**
	 * @see MapsBaseMap::doMapServiceLoad()
	 *
	 */		
	protected function doMapServiceLoad() {
		global $egGoogleMapsOnThisPage;
		
		self::addGMapDependencies($this->output);
		$egGoogleMapsOnThisPage++;
		
		$this->elementNr = $egGoogleMapsOnThisPage;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 *
	 */	
	public function addSpecificMapHTML() {
		global $wgJsMimeType;
		
		$enableEarth = self::getEarthValue($this->earth);
		$this->earth = self::getJSEarthValue($enableEarth);
		
		$this->type = self::getGMapType($this->type, $enableEarth);
		$control = self::getGControlType($this->controls);	
		
		$this->autozoom = ($this->autozoom == 'no' || $this->autozoom == 'off') ? 'false' : 'true';	
		
		$markerItems = array();		
		
		foreach ($this->markerData as $markerData) {
			$lat = $markerData['lat'];
			$lon = $markerData['lon'];
			$markerItems[] = "getGMarkerData($lat, $lon, '$this->title', '$this->label')";
		}		
		
		$markersString = implode(',', $markerItems);	
		
		$this->output .=<<<END

<div id="$this->mapName" class="$this->class" style="$this->style" ></div>
<script type="$wgJsMimeType"> /*<![CDATA[*/
addLoadEvent(
	initializeGoogleMap('$this->mapName', $this->width, $this->height, $this->centre_lat, $this->centre_lon, $this->zoom, $this->type, new $control(), $this->autozoom, $this->earth, [$markersString])
);
/*]]>*/ </script>

END;

	}
	
}

