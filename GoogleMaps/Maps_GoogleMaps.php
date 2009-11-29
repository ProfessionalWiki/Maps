<?php

/**
 * This groupe contains all Google Maps related files of the Maps extension.
 * 
 * @defgroup MapsGoogleMaps Google Maps
 * @ingroup Maps
 */

/**
 * This file holds the general information for the Google Maps service
 *
 * @file Maps_GoogleMaps.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['googlemaps'] = array(
									'pf' => array(
										'display_point' => array('class' => 'MapsGoogleMapsDispPoint', 'file' => 'GoogleMaps/Maps_GoogleMapsDispPoint.php', 'local' => true),
										'display_map' => array('class' => 'MapsGoogleMapsDispMap', 'file' => 'GoogleMaps/Maps_GoogleMapsDispMap.php', 'local' => true),
										),
									'classes' => array(
											array('class' => 'MapsGoogleMaps', 'file' => 'GoogleMaps/Maps_GoogleMaps.php', 'local' => true)
											),
									'aliases' => array('google', 'googlemap', 'gmap', 'gmaps'),
									);	

/**
 * Class for Google Maps initialization.
 * 
 * @ingroup MapsGoogleMaps
 * 
 * @author Jeroen De Dauw
 */											
class MapsGoogleMaps {
	
	const SERVICE_NAME = 'googlemaps';	
	
	public static function initialize() {
		self::initializeParams();
	}
	
	private static function initializeParams() {
		global $egMapsServices, $egMapsGoogleMapsType, $egMapsGoogleMapsTypes, $egMapsGoogleAutozoom, $egMapsGoogleMapsZoom, $egMapsGMapControls;
		
		$allowedTypes = MapsGoogleMaps::getTypeNames();
		
		$egMapsServices[self::SERVICE_NAME]['parameters'] = array(
									'zoom' => array(
										'default' => $egMapsGoogleMapsZoom,
										),
									'controls' => array(
										'criteria' => array(), // TODO 
										'default' => implode(',', $egMapsGMapControls)			
										),
									'type' => array (
										'aliases' => array('map-type', 'map type'),
										'criteria' => array(
											'in_array' => $allowedTypes		
											),
										'default' => $egMapsGoogleMapsType												
										),
									'types' => array (
										'aliases' => array('map-types', 'map types'),
										'criteria' => array(
											'all_str_in_array' => array(',', $allowedTypes)
											),
										'default' => implode(',', $egMapsGoogleMapsTypes)									
										),
									'autozoom' => array(
										'aliases' => array('auto zoom', 'mouse zoom', 'mousezoom'),
										'criteria' => array(
											'in_array' => array('on', 'off', 'yes', 'no')	
											),		
										'default' => $egMapsGoogleAutozoom ? 'on' : 'off'										
										),									
									'class' => array(
										'aliases' => array(),
										'criteria' => array(),		
										'default' => '' 												
										),
									'style' => array(
										'aliases' => array(),
										'criteria' => array(),		
										'default' => '' 												
										),
									);
	}

	// http://code.google.com/apis/maps/documentation/reference.html#GMapType.G_NORMAL_MAP
	private static $mapTypes = array(
					'normal' => 'G_NORMAL_MAP',
					'satellite' => 'G_SATELLITE_MAP',
					'hybrid' => 'G_HYBRID_MAP',
					'terrain' => 'G_PHYSICAL_MAP',
					'physical' => 'G_PHYSICAL_MAP',
					'earth' => 'G_SATELLITE_3D_MAP',
					'sky' => 'G_SKY_VISIBLE_MAP',
					'moon' => 'G_MOON_VISIBLE_MAP',
					'moon-elevation' => 'G_MOON_ELEVATION_MAP',
					'mars' => 'G_MARS_VISIBLE_MAP',
					'mars-elevation' => 'G_MARS_ELEVATION_MAP',
					'mars-infrared' => 'G_MARS_INFRARED_MAP'
					);

	private static $overlayData = array(
					'photos' => '0',
					'videos' => '1',
					'wikipedia' => '2',
					'webcams' => '3'
					);

	/**
	 * Returns the names of all supported map types.
	 * 
	 * @return array
	 */					
	public static function getTypeNames() {
		return array_keys(self::$mapTypes);
	} 

	/**
	 * Returns the Google Map type (defined in MapsGoogleMaps::$mapTypes) 
	 * for the provided a general map type. When no match is found, false
	 * will be returned.
	 *
	 * @param string $type
	 * @param boolean $restoreAsDefault
	 * 
	 * @return string or false
	 */
	public static function getGMapType($type, $restoreAsDefault = false) {
		global $egMapsGoogleMapsType;
		$typeIsValid = array_key_exists($type, self::$mapTypes);
		
		if ($typeIsValid) {
			return self::$mapTypes[ $type ];
		}
		else {
			if ($restoreAsDefault) {
				return self::$mapTypes[ $egMapsGoogleMapsType ]; 
			}
			else {
				return false;
			}
		}
	}	
	
	/**
	 * Add references to the Google Maps API and required JS file to the provided output 
	 *
	 * @param string $output
	 */
	public static function addGMapDependencies(&$output) {
		global $wgJsMimeType, $wgLang;
		global $egGoogleMapsKey, $egMapsScriptPath, $egGoogleMapsOnThisPage, $egMapsStyleVersion;

		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;

			MapsGoogleMaps::validateGoogleMapsKey();

			// TODO: use strbuilder for performance gain?
			$output .= "<script src='http://maps.google.com/maps?file=api&v=2&key=$egGoogleMapsKey&hl={$wgLang->getCode()}' type='$wgJsMimeType'></script>
			<script type='$wgJsMimeType' src='$egMapsScriptPath/GoogleMaps/GoogleMapFunctions.js?$egMapsStyleVersion'></script>
			<script type='$wgJsMimeType'>window.unload = GUnload;</script>";
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
	 * Returns a JS items string with the provided types. The earth type will
	 * be added to it when it's not present and $enableEarth is true. If there are
	 * no types, the default will be used.
	 *
	 * @param array $types
	 * @param boolean $enableEarth
	 * @return string
	 */
	public static function createTypesString(array &$types) {	
		global $egMapsGoogleMapsTypes, $egMapsGoogleMapTypesValid;
		
		$types = MapsMapper::getValidTypes($types, $egMapsGoogleMapsTypes, $egMapsGoogleMapTypesValid, array(__CLASS__, 'getGMapType'));
			
		return MapsMapper::createJSItemsString($types, null, false, false);
	}
	
	/**
	 * This function ensures backward compatibility with Semantic Google Maps and other extensions
	 * using $wgGoogleMapsKey instead of $egGoogleMapsKey.
	 */
	public static function validateGoogleMapsKey() {
		global $egGoogleMapsKey, $wgGoogleMapsKey;
		
		if (isset($wgGoogleMapsKey)){
			if (strlen(trim($egGoogleMapsKey)) < 1) $egGoogleMapsKey = $wgGoogleMapsKey;
		} 
	}	
	
	/**
	 * Adds the needed output for the overlays control.
	 * 
	 * @param string $output
	 * @param string $mapName
	 * @param string $overlays
	 * @param string $controls
	 */
	public static function addOverlayOutput(&$output, $mapName, $overlays, $controls) {
		global $egMapsGMapOverlays, $egMapsGoogleOverlLoaded, $wgJsMimeType;

		// Check to see if there is an overlays control.
		$hasOverlayControl = in_string('overlays', $controls);
		
		$overlayNames = array_keys(self::$overlayData);
		
		// Create the overlays array, and use the default in case no overlays have been provided.
		if (strlen(trim($overlays)) < 1) {
			$overlays = $egMapsGMapOverlays;
		} else {
			MapsMapper::enforceArrayValues($overlays);
			$validOverlays = array();
			foreach ($overlays as $overlay) {
				$segements = explode('-', $overlay);
				$name = $segements[0];
				
				if (in_array($name, $overlayNames)) {
					$isOn = count($segements) > 1 ? $segements[1] : '0';
					$validOverlays[$name] = $isOn == '1';
				}
			} 
			$overlays = $validOverlays;
		}
		
		// If there are no overlays or there is no control to hold them, don't bother the rest.
		if(!$hasOverlayControl || count($overlays) < 1) return;
		
		// If the overlays JS and CSS has not yet loaded, do it.
		if (empty($egMapsGoogleOverlLoaded)) {
			$egMapsGoogleOverlLoaded = true;
			MapsGoogleMaps::addOverlayCss($output);
		}
		
		// Add the inputs for the overlays.
		$addedOverlays = array();
		$overlayHtml = '';
		$onloadFunctions = '';
		foreach ($overlays as $overlay => $isOn) {
			$overlay = strtolower($overlay);
			
			if (in_array($overlay, $overlayNames)) {
				if (! in_array($overlay, $addedOverlays)) {
					$addedOverlays[] = $overlay;
					$label = wfMsg('maps_' . $overlay);
					$urlNr = self::$overlayData[$overlay];
					$overlayHtml .= "<input id='$mapName-overlay-box-$overlay' name='$mapName-overlay-box' type='checkbox' onclick='switchGLayer(GMaps[\"$mapName\"], this.checked, GOverlays[$urlNr])' /> $label <br />";
					if ($isOn) {
						$onloadFunctions .= "<script type='$wgJsMimeType'>addOnloadHook( initiateGOverlay('$mapName-overlay-box-$overlay', '$mapName', $urlNr) );</script>";
					}
				}				
			}
		}
		
		$output .=<<<END
<script type='$wgJsMimeType'>var timer_$mapName;</script>		
<div class='outer-more' id='$mapName-outer-more'><form action=''><div class='more-box' id='$mapName-more-box'>
$overlayHtml
</div></form></div>		
END;

	return $onloadFunctions;
	}
	
	/**
	 * 
	 * 
	 * @param $output
	 * @return unknown_type
	 */
	private static function addOverlayCss(&$output) {
		$css =<<<END

<style type="text/css">
.inner-more {
	text-align:center;
	font-size:12px;
	background-color: #fff;
	color: #000;
	border: 1px solid #fff;
	border-right-color: #b0b0b0;
	border-bottom-color: #c0c0c0;
	width:7em;
	cursor: pointer;
}

.inner-more.highlight {
	font-weight: bold;
	border: 1px solid #483D8B;
	border-right-color: #6495ed;
	border-bottom-color: #6495ed;
} 

.more-box {  position:absolute;
	top:25px; left:0px;
	margin-top:-1px;
	font-size:12px;
	padding: 6px 4px;
	width:120px;
	background-color: #fff;
	color: #000;
	border: 1px solid gray;
	border-top:1px solid #e2e2e2;
	display: none;
	cursor:default;
}

.more-box.highlight {
	width:119px;
	border-width:2px;
}	
</style>	

END;

	$output .= preg_replace('/\s+/m', ' ', $css);
	}	
	
}
									