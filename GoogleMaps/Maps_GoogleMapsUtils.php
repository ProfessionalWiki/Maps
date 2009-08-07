<?php

/**
 * A class that holds static helper functions for Google Maps
 *
 * @file Maps_GooleMapsUtils.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class MapsGoogleMapsUtils {
	
	// http://code.google.com/apis/maps/documentation/reference.html#GMapType.G_NORMAL_MAP
	// TODO: Add a true alliasing system? Might be overkill.
	private static $mapTypes = array(
					'normal' => 'G_NORMAL_MAP',
					'G_NORMAL_MAP' => 'G_NORMAL_MAP',
	
					'satellite' => 'G_SATELLITE_MAP',
					'G_SATELLITE_MAP' => 'G_SATELLITE_MAP',
	
					'hybrid' => 'G_HYBRID_MAP',
					'G_HYBRID_MAP' => 'G_HYBRID_MAP',
	
					'physical' => 'G_PHYSICAL_MAP',
					'G_PHYSICAL_MAP' => 'G_PHYSICAL_MAP',
	
					'earth' => 'G_SATELLITE_3D_MAP',
					'G_SATELLITE_3D_MAP' => 'G_SATELLITE_3D_MAP',
	
					'sky' => 'G_SKY_VISIBLE_MAP',
					'G_SKY_VISIBLE_MAP' => 'G_SKY_VISIBLE_MAP',	
	
					'moon' => 'G_MOON_VISIBLE_MAP',
					'G_MOON_VISIBLE_MAP' => 'G_MOON_VISIBLE_MAP',

					'moon-elevation' => 'G_MOON_ELEVATION_MAP',
					'G_MOON_ELEVATION_MAP' => 'G_MOON_ELEVATION_MAP',
	
					'mars' => 'G_MARS_VISIBLE_MAP',
					'G_MARS_VISIBLE_MAP' => 'G_MARS_VISIBLE_MAP',

					'mars-elevation' => 'G_MARS_ELEVATION_MAP',
					'G_MARS_ELEVATION_MAP' => 'G_MARS_ELEVATION_MAP',
	
					'mars-infrared' => 'G_MARS_INFRARED_MAP',
					'G_MARS_INFRARED_MAP' => 'G_MARS_INFRARED_MAP',	
					);

	// http://code.google.com/apis/maps/documentation/controls.html#Controls_overview
	private static $controlClasses = array(
					'large' => 'GLargeMapControl3D',
					'small' => 'GSmallZoomControl3D',
					);
	
	/**
	 * Returns the Google Map type (defined in MapsGoogleMaps::$mapTypes) 
	 * for the provided a general map type. When no match is found, the
	 * default map type will be returned.
	 *
	 * @param string $type
	 * @param boolean $earthEnabled
	 * @return string
	 */
	public static function getGMapType($type, $earthEnabled = false) {
		global $egMapsGoogleMapsType;
		
		if (! array_key_exists($type, self::$mapTypes)) {
			$type = $earthEnabled ? "earth" : $egMapsGoogleMapsType;
		}
		
		return self::$mapTypes[ $type ];
	}
	
	/**
	 * Returns the Google Map Control type (defined in MapsGoogleMaps::$controlClasses) 
	 * for the provided a general map control type. When no match is found, the provided
	 * control name will be used.
	 *
	 * @param array $controls
	 * @return string
	 */
	public static function getGControlType(array $controls) {
		global $egMapsGMapControl;
		$control = count($controls) > 0 ? $controls[0] : $egMapsGMapControl;
		return array_key_exists($control, self::$controlClasses) ? self::$controlClasses[$control] : $control; 
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
			'types' => array(),			
			'class' => 'pmap',
			'autozoom' => '',
			'earth' => '',
			'style' => ''			
			); 		
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
	 * Retuns a boolean as string, true if $autozoom is on or yes.
	 *
	 * @param string $autozoom
	 * @return string
	 */
	public static function getAutozoomJSValue($autozoom) {
		return MapsMapper::getJSBoolValue(in_array($autozoom, array('on', 'yes')));
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
	
	
}