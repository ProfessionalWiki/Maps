<?php

/**
 * File holding the MapsGoogleMaps class.
 *
 * @file Maps_GoogleMaps.php
 * @ingroup MapsGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * TODO
 * 
 * @ingroup MapsGoogleMaps
 * 
 * @author Jeroen De Dauw
 */
class MapsGoogleMaps extends MapsMappingService {
	
	function __construct() {
		parent::__construct(
			'googlemaps2',
			array( 'googlemaps', 'google', 'googlemap', 'gmap', 'gmaps' )
		);
	}
	
	protected function initParameterInfo( array &$parameters ) {
		global $egMapsServices, $egMapsGoogleMapsType, $egMapsGoogleMapsTypes, $egMapsGoogleAutozoom, $egMapsGMapControls;
		
		Validator::addOutputFormat( 'gmaptype', array( __CLASS__, 'setGMapType' ) );
		Validator::addOutputFormat( 'gmaptypes', array( __CLASS__, 'setGMapTypes' ) );
		
		Validator::addValidationFunction( 'is_google_overlay', array( __CLASS__, 'isGOverlay' ) );		
		
		$allowedTypes = self::getTypeNames();
		
		$parameters = array(
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
		
		$parameters['zoom']['criteria']['in_range'] = array( 0, 20 );
	}

	// http://code.google.com/apis/maps/documentation/reference.html#GMapType.G_NORMAL_MAP
	protected static $mapTypes = array(
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

	protected static $overlayData = array(
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
	public static function isGOverlay( $value, $name, array $parameters ) {
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
	public static function setGMapType( &$type, $name, array $parameters ) {
		$type = self::$mapTypes[ $type ];
	}
	
	/**
	 * Changes the map type names into the corresponding Google Maps API v2 identifiers.
	 * 
	 * @param array $types
	 * 
	 * @return array
	 */
	public static function setGMapTypes( array &$types, $name, array $parameters ) {
		for ( $i = count( $types ) - 1; $i >= 0; $i-- ) {
			$types[$i] = self::$mapTypes[ $types[$i] ];
		}
	}
	
	/**
	 * @see MapsMappingService::getDependencies
	 * 
	 * @return array
	 */
	protected function getDependencies() {
		global $wgLang;
		global $egGoogleMapsKey, $egMapsStyleVersion, $egMapsJsExt, $egMapsScriptPath;
		
		$langCode = self::getMappedLanguageCode( $wgLang->getCode() ); 
		
		return array(
			Html::linkedScript( "http://maps.google.com/maps?file=api&v=2&key=$egGoogleMapsKey&hl=$langCode" ),
			Html::linkedScript( "$egMapsScriptPath/Services/GoogleMaps/GoogleMapFunctions{$egMapsJsExt}?$egMapsStyleVersion" ),
			Html::inlineScript( 'window.unload = GUnload;' )
		);
	}
	
	/**
	 * Maps language codes to Google Maps API v2 compatible values.
	 * 
	 * @param string $code
	 * 
	 * @return string The mapped code
	 */
	protected static function getMappedLanguageCode( $code ) {
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
	 * 
	 * FIXME: layer onload function kills maps for some reason
	 */
	public function addOverlayOutput( &$output, $mapName, $overlays, $controls ) {
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
		
		// Add the inputs for the overlays.
		$addedOverlays = array();
		$overlayHtml = '';
		$onloadFunctions = array();
		
		foreach ( $overlays as $overlay => $isOn ) {
			$overlay = strtolower( $overlay );
			
			if ( in_array( $overlay, $overlayNames ) ) {
				if ( !in_array( $overlay, $addedOverlays ) ) {
					$addedOverlays[] = $overlay;
					$label = wfMsg( 'maps_' . $overlay );
					$urlNr = self::$overlayData[$overlay];
					
					$overlayHtml .= Html::input(
						"$mapName-overlay-box",
						null,
						'checkbox',
						array(
							'id' => "$mapName-overlay-box-$overlay",
							'onclick' => "switchGLayer(GMaps['$mapName'], this.checked, GOverlays[$urlNr])"
						)
					) . htmlspecialchars( $label ) . '<br />' ;
					
					if ( $isOn ) {
						$onloadFunctions[] = "addOnloadHook( function() { initiateGOverlay('$mapName-overlay-box-$overlay', '$mapName', $urlNr) } );";
					}
				}
			}
		}
		
		$output .= Html::rawElement(
			'div',
			array(
				'class' => 'outer-more',
				'id' => htmlspecialchars( "$mapName-outer-more" )
			),
			'<form action="">' .
			Html::rawElement(
				'div',
				array(
					'class' => 'more-box',
					'id' => htmlspecialchars( "$mapName-more-box" )
				),
				$overlayHtml
			) .
			'</form>'
		);
			
		// If the overlays JS and CSS has not yet loaded, do it.
		if ( empty( $egMapsGoogleOverlLoaded ) ) {
			$egMapsGoogleOverlLoaded = true;
			
			$this->addDependency(
				$this->getOverlayCss()
				. Html::inlineScript( 'var timer_' . htmlspecialchars( $mapName ) . ';' . implode( "\n", $onloadFunctions ) )
			);
		}		
	}
	
	/**
	 * Returns CSS for the overlays. 
	 */
	protected function getOverlayCss() {
		return Html::inlineStyle( <<<EOT
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
EOT
		);
	}
	
}								