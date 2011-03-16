<?php

/**
 * Class holding information and functionallity specific to Google Maps v3.
 * This infomation and features can be used by any mapping feature. 
 * 
 * @since 0.7
 * 
 * @file Maps_GoogleMaps3.php
 * @ingroup MapsGoogleMaps3
 * 
 * @author Jeroen De Dauw
 */
class MapsGoogleMaps3 extends MapsMappingService {
	
	/**
	 * List of map types (keys) and their internal values (values). 
	 * 
	 * @since 0.7
	 * 
	 * @var array
	 */
	public static $mapTypes = array(
		'normal' => 'ROADMAP',
		'roadmap' => 'ROADMAP',
		'satellite' => 'SATELLITE',
		'hybrid' => 'HYBRID',
		'terrain' => 'TERRAIN',
		'physical' => 'TERRAIN'
	);
	
	/**
	 * List of supported map layers. 
	 * 
	 * @since 0.8
	 * 
	 * @var array
	 */
	protected static $mapLayers = array(
		'traffic',
		'bicycling'
	);	
	
	public static $tyepControlStyles = array(
		'default' => 'DEFAULT',
		'horizontal' => 'HORIZONTAL_BAR',
		'dropdown' => 'DROPDOWN_MENU'
	);
	
	/**
	 * List of supported control names.
	 * 
	 * @since 0.8
	 * 
	 * @var array
	 */
	protected static $controlNames = array(
		'pan',
		'zoom',
		'type',
		'scale',
		'streetview'
	);		
	
	/**
	 * Constructor.
	 * 
	 * @since 0.6.6
	 */	
	function __construct( $serviceName ) {
		parent::__construct(
			$serviceName,
			array( 'googlemaps', 'google' )
		);
	}
	
	/**
	 * @see MapsMappingService::addParameterInfo
	 * 
	 * @since 0.7
	 */	
	public function addParameterInfo( array &$params ) {
		global $egMapsGMaps3Type, $egMapsGMaps3Types, $egMapsGMaps3Controls, $egMapsGMaps3Layers;
		global $egMapsGMaps3DefTypeStyle, $egMapsGMaps3DefZoomStyle, $egMapsGMaps3AutoInfoWindows;
		global $egMapsResizableByDefault;
		
		$params['zoom']->addCriteria( new CriterionInRange( 0, 20 ) );
		$params['zoom']->setDefault( self::getDefaultZoom() );		
		
		$params['type'] = new Parameter( 'type' );
		$params['type']->setDefault( $egMapsGMaps3Type );
		$params['type']->addCriteria( new CriterionInArray( self::getTypeNames() ) );
		$params['type']->addManipulations( new MapsParamGMap3Type() );
		
		$params['types'] = new ListParameter( 'types' );
		$params['types']->setDefault( $egMapsGMaps3Types );
		$params['types']->addCriteria( new CriterionInArray( self::getTypeNames() ) );		
		$params['types']->addManipulations( new MapsParamGMap3Type() );
		
		$params['layers'] = new ListParameter( 'layers' );
		$params['layers']->setDefault( $egMapsGMaps3Layers );
		$params['layers']->addCriteria( new CriterionInArray( self::getLayerNames() ) );		
		
		$params['controls'] = new ListParameter( 'controls' );
		$params['controls']->setDefault( $egMapsGMaps3Controls );
		$params['controls']->addCriteria( new CriterionInArray( self::$controlNames ) );
		$params['controls']->addManipulations( new ParamManipulationFunctions( 'strtolower' ) );
		
		$params['zoomstyle'] = new Parameter( 'zoomstyle' );
		$params['zoomstyle']->setDefault( $egMapsGMaps3DefZoomStyle );
		$params['zoomstyle']->addCriteria( new CriterionInArray( 'default', 'small', 'large' ) );
		$params['zoomstyle']->addManipulations( new MapsParamGMap3Zoomstyle() );
		
		$params['typestyle'] = new Parameter( 'typestyle' );
		$params['typestyle']->setDefault( $egMapsGMaps3DefTypeStyle );
		$params['typestyle']->addCriteria( new CriterionInArray( array_keys( self::$tyepControlStyles ) ) );
		$params['typestyle']->addManipulations( new MapsParamGMap3Typestyle() );

		$params['autoinfowindows'] = new Parameter( 'autoinfowindows', Parameter::TYPE_BOOLEAN );
		$params['autoinfowindows']->setDefault( $egMapsGMaps3AutoInfoWindows );
		
		$params['kml'] = new ListParameter( 'kml' );
		$params['kml']->setDefault( array() );
		//$params['kml']->addManipulations( new MapsParamFile() );	

		$params['fusiontables'] = new ListParameter( 'fusiontables' );
		$params['fusiontables']->setDefault( array() );

		$params['resizable'] = new Parameter( 'resizable', Parameter::TYPE_BOOLEAN );
		$params['resizable']->setDefault( $egMapsResizableByDefault, false );		
	}
	
	/**
	 * @see iMappingService::getDefaultZoom
	 * 
	 * @since 0.6.5
	 */	
	public function getDefaultZoom() {
		global $egMapsGMaps3Zoom;
		return $egMapsGMaps3Zoom;
	}	
	
	/**
	 * @see MapsMappingService::getMapId
	 * 
	 * @since 0.6.5
	 */
	public function getMapId( $increment = true ) {
		static $mapsOnThisPage = 0;
		
		if ( $increment ) {
			$mapsOnThisPage++;
		}
		
		return 'map_google3_' . $mapsOnThisPage;
	}	
	
	/**
	 * Returns the names of all supported map types.
	 * 
	 * @return array
	 */
	public static function getTypeNames() {
		return array_keys( self::$mapTypes );
	}
	
	/**
	 * Returns the names of all supported map layers.
	 * 
	 * @since 0.8
	 * 
	 * @return array
	 */
	public static function getLayerNames() {
		return self::$mapLayers;
	}	
	
	/**
	 * @see MapsMappingService::getDependencies
	 * 
	 * @return array
	 */
	protected function getDependencies() {
		global $wgLang;
		global $egMapsStyleVersion, $egMapsScriptPath;

		$languageCode = self::getMappedLanguageCode( $wgLang->getCode() );
		
		return array(
			Html::linkedScript( "http://maps.google.com/maps/api/js?sensor=false&language=$languageCode" )
		);			
	}
	
	/**
	 * Maps language codes to Google Maps API v3 compatible values.
	 * 
	 * @param string $code
	 * 
	 * @return string The mapped code
	 */
	protected static function getMappedLanguageCode( $code ) {
		$mappings = array(
	         'en_gb' => 'en-gb',// v3 supports en_gb - but wants us to call it en-gb
	         'he' => 'iw',      // iw is googlish for hebrew
	         'fj' => 'fil',     // google does not support Fijian - use Filipino as close(?) supported relative
		);
		
		if ( array_key_exists( $code, $mappings ) ) {
			$code = $mappings[$code];
		}
		
		return $code;
	}
	
	/**
	 * @see MapsMappingService::getResourceModules
	 * 
	 * @since 0.8
	 * 
	 * @return array of string
	 */
	public function getResourceModules() {
		return array_merge(
			parent::getResourceModules(),
			array( 'ext.maps.googlemaps3' )
		);
	}	
	
}
