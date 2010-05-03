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

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsGoogleMaps'] = dirname( __FILE__ ) . '/Maps_GoogleMaps.php';

$wgHooks['MappingServiceLoad'][] = 'MapsGoogleMaps::initialize';

$wgAutoloadClasses['MapsGoogleMapsDispMap'] = dirname( __FILE__ ) . '/Maps_GoogleMapsDispMap.php';
$wgAutoloadClasses['MapsGoogleMapsDispPoint'] = dirname( __FILE__ ) . '/Maps_GoogleMapsDispPoint.php';

$egMapsServices[MapsGoogleMaps::SERVICE_NAME] = array(
	'aliases' => array( 'googlemaps', 'google', 'googlemap', 'gmap', 'gmaps' ),
	'features' => array(
		'display_point' => 'MapsGoogleMapsDispPoint',
		'display_map' => 'MapsGoogleMapsDispMap',
	)
);

/**
 * Class for Google Maps initialization.
 * 
 * @ingroup MapsGoogleMaps
 * 
 * @author Jeroen De Dauw
 */
class MapsGoogleMaps {
	
	const SERVICE_NAME = 'googlemaps2';
	
	public static function initialize() {
		global $wgAutoloadClasses, $egMapsServices;
		
		self::initializeParams();
		
		Validator::addOutputFormat( 'gmaptype', array( __CLASS__, 'setGMapType' ) );
		Validator::addOutputFormat( 'gmaptypes', array( __CLASS__, 'setGMapTypes' ) );
		
		Validator::addValidationFunction( 'is_google_overlay', array( __CLASS__, 'isGOverlay' ) );
		
		return true;
	}
	
	private static function initializeParams() {
		global $egMapsServices, $egMapsGoogleMapsType, $egMapsGoogleMapsTypes, $egMapsGoogleAutozoom, $egMapsGMapControls;
		
		$allowedTypes = self::getTypeNames();
		
		$egMapsServices[self::SERVICE_NAME]['parameters'] = array(
			'controls' => array(
				'type' => array( 'string', 'list' ),
				'criteria' => array(
					'in_array' => self::getControlNames()
				),
				'default' => $egMapsGMapControls,
				'output-type' => array( 'list', ',', '\'' )
			),
			'type' => array(
				'aliases' => array( 'map-type', 'map type' ),
				'criteria' => array(
					'in_array' => $allowedTypes
				),
				'default' => $egMapsGoogleMapsType, // FIXME: default value should not be used when not present in types parameter.
				'output-type' => 'gmaptype',
				'dependencies' => array( 'types' )
			),
			'types' => array(
				'type' => array( 'string', 'list' ),
				'aliases' => array( 'map-types', 'map types' ),
				'criteria' => array(
					'in_array' => $allowedTypes
				),
				'default' => $egMapsGoogleMapsTypes,
				'output-types' => array( 'gmaptypes', 'list' )
			),
			'autozoom' => array(
				'type' => 'boolean',
				'aliases' => array( 'auto zoom', 'mouse zoom', 'mousezoom' ),
				'default' => $egMapsGoogleAutozoom,
				'output-type' => 'boolstr'
			),
		);
		
		$egMapsServices[self::SERVICE_NAME]['parameters']['zoom']['criteria']['in_range'] = array( 0, 20 );
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
		return array_keys( self::$mapTypes );
	}
	
	/**
	 * Returns the names of all supported controls. 
	 * This data is a copy of the one used to actually translate the names
	 * into the controls, since this resides client side, in GoogleMapFunctions.js. 
	 * 
	 * @return array
	 */
	public static function getControlNames() {
		return array(
			'auto', 'large', 'small', 'large-original', 'small-original', 'zoom', 'type', 'type-menu',
			'overlays', 'overview', 'overview-map', 'scale', 'nav-label', 'nav'
		);
	}

	/**
	 * Returns the names of all supported map overlays.
	 * 
	 * @return array
	 */
	public static function getOverlayNames() {
		return array_keys( self::$overlayData );
	}
	
	/**
	 * Returns whether the provided value is a valid google overlay.
	 * 
	 * @param $value
	 * 
	 * @return boolean
	 */
	public static function isGOverlay( $value, array $metaData ) {
		$value = explode( '-', $value );
		if ( count( $value ) > 2 ) return false;
		if ( count( $value ) > 1 && !in_array( $value[1], array( '0', '1' ) ) ) return false;
		return in_array( $value[0], self::getOverlayNames() );
	}

	/**
	 * Changes the map type name into the corresponding Google Maps API v2 identifier.
	 *
	 * @param string $type
	 * 
	 * @return string
	 */
	public static function setGMapType( &$type ) {
		$type = self::$mapTypes[ $type ];
	}
	
	/**
	 * Changes the map type names into the corresponding Google Maps API v2 identifiers.
	 * 
	 * @param array $types
	 * 
	 * @return array
	 */
	public static function setGMapTypes( array &$types ) {
		for ( $i = count( $types ) - 1; $i >= 0; $i-- ) {
			$types[$i] = self::$mapTypes[ $types[$i] ];
		}
	}
	
	/**
	 * Add references to the Google Maps API and required JS file to the provided output 
	 *
	 * @param string $output
	 */
	public static function addGMapDependencies( &$output, Parser &$parser ) {
		global $wgJsMimeType, $wgLang;
		global $egGoogleMapsKey, $egGoogleMapsOnThisPage, $egMapsStyleVersion, $egMapsJsExt, $egMapsScriptPath;

		if ( empty( $egGoogleMapsOnThisPage ) ) {
			$egGoogleMapsOnThisPage = 0;

			MapsGoogleMaps::validateGoogleMapsKey();

			$langCode = self::getMappedLanguageCode( $wgLang->getCode() );
			
			$parser->getOutput()->addHeadItem( 
				Html::element(
					'script', 
					array(
						'type' => $wgJsMimeType,
						'src' => "http://maps.google.com/maps?file=api&amp;v=2&amp;key=$egGoogleMapsKey&amp;hl=$langCode"
					)
				) .	
				Html::element(
					'script', 
					array(
						'type' => $wgJsMimeType,
						'src' => "$egMapsScriptPath/Services/GoogleMaps/GoogleMapFunctions{$egMapsJsExt}?$egMapsStyleVersion"
					)
				) .								
				Html::inlineScript( 'window.unload = GUnload;' )
			);
		}
	}
	
	/**
	 * Maps language codes to Google Maps API v2 compatible values.
	 * 
	 * @param string $code
	 * 
	 * @return string The mapped code
	 */
	private static function getMappedLanguageCode( $code ) {
		$mappings = array(
	         'en_gb' => 'en',// v2 does not support en_gb - use english :(
	         'he' => 'iw',   // iw is googlish for hebrew
	         'fj' => 'fil',  // google does not support Fijian - use Filipino as close(?) supported relative
	         'or' => 'en'    // v2 does not support Oriya.
		);
		
		if ( array_key_exists( $code, $mappings ) ) {
			$code = $mappings[$code];
		}
		
		return $code;
	}
	
	/**
	 * This function ensures backward compatibility with Semantic Google Maps and other extensions
	 * using $wgGoogleMapsKey instead of $egGoogleMapsKey.
	 * 
	 * FIXME: Possible vunerability when register globals is on.
	 */
	public static function validateGoogleMapsKey() {
		global $egGoogleMapsKey, $wgGoogleMapsKey;
		
		if ( isset( $wgGoogleMapsKey ) ) {
			if ( strlen( trim( $egGoogleMapsKey ) ) < 1 ) $egGoogleMapsKey = $wgGoogleMapsKey;
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
	public static function addOverlayOutput( &$output, $mapName, $overlays, $controls ) {
		global $egMapsGMapOverlays, $egMapsGoogleOverlLoaded, $wgJsMimeType;
		
		// Check to see if there is an overlays control.
		$hasOverlayControl = in_string( 'overlays', $controls );
		
		$overlayNames = array_keys( self::$overlayData );
		
		$validOverlays = array();
		foreach ( $overlays as $overlay ) {
			$segements = explode( '-', $overlay );
			$name = $segements[0];
			
			if ( in_array( $name, $overlayNames ) ) {
				$isOn = count( $segements ) > 1 ? $segements[1] : '0';
				$validOverlays[$name] = $isOn == '1';
			}
		}
		$overlays = $validOverlays;
		
		// If there are no overlays or there is no control to hold them, don't bother the rest.
		if ( !$hasOverlayControl || count( $overlays ) < 1 ) return;
		
		// If the overlays JS and CSS has not yet loaded, do it.
		if ( empty( $egMapsGoogleOverlLoaded ) ) {
			$egMapsGoogleOverlLoaded = true;
			MapsGoogleMaps::addOverlayCss( $output );
		}
		
		// Add the inputs for the overlays.
		$addedOverlays = array();
		$overlayHtml = '';
		$onloadFunctions = array();
		foreach ( $overlays as $overlay => $isOn ) {
			$overlay = strtolower( $overlay );
			
			if ( in_array( $overlay, $overlayNames ) ) {
				if ( ! in_array( $overlay, $addedOverlays ) ) {
					$addedOverlays[] = $overlay;
					$label = wfMsg( 'maps_' . $overlay );
					$urlNr = self::$overlayData[$overlay];
					$overlayHtml .= "<input id='$mapName-overlay-box-$overlay' name='$mapName-overlay-box' type='checkbox' onclick='switchGLayer(GMaps[\"$mapName\"], this.checked, GOverlays[$urlNr])' /> $label <br />";
					if ( $isOn ) {
						$onloadFunctions[] = "addOnloadHook( function() { initiateGOverlay('$mapName-overlay-box-$overlay', '$mapName', $urlNr) } );";
					}
				}
			}
		}
		
		$output .= <<<EOT
<script type='$wgJsMimeType'>var timer_$mapName;</script>		
<div class='outer-more' id='$mapName-outer-more'><form action=''><div class='more-box' id='$mapName-more-box'>
$overlayHtml
</div></form></div>		
EOT;

		if ( count( $onloadFunctions ) > 0 ) {
			$output .= "<script type='$wgJsMimeType'>" . implode( "\n", $onloadFunctions ) . '</script>';
		}
	}
	
	/**
	 * Add CSS for the overlays. 
	 * 
	 * @param $output
	 * 
	 * TODO
	 */
	private static function addOverlayCss( &$output ) {
		$css = <<<END

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

	$output .= preg_replace( '/\s+/m', ' ', $css );
	}
	
}
									