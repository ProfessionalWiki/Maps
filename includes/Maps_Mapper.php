<?php

/**
 * A class that holds static helper functions for generic mapping-related functions.
 * 
 * @since 0.1
 * 
 * @deprecated
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsMapper {
	
	/**
	 * This function returns the definitions for the parameters used by every map feature.
	 *
	 * @deprecated
	 *
	 * @return array
	 */
	public static function getCommonParameters() {
		global $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsMapWidth, $egMapsMapHeight, $egMapsDefaultService;

		$params = array();

		$params['mappingservice'] = array(
			'type' => 'mappingservice',
			'aliases' => 'service',
			'default' => $egMapsDefaultService,
		);

		$params['geoservice'] = array(
			'default' => $egMapsDefaultGeoService,
			'values' => $egMapsAvailableGeoServices,
			'dependencies' => 'mappingservice',
			// TODO 'manipulations' => new MapsParamGeoService( 'mappingservice' ),
		);

		$params['width'] = array(
			'type' => 'dimension',
			'allowauto' => true,
			'default' => $egMapsMapWidth,
		);

		$params['height'] = array(
			'type' => 'dimension',
			'default' => $egMapsMapHeight,
		);

		// TODO$manipulation = new MapsParamLocation();
		// TODO$manipulation->toJSONObj = true;

		$params['centre'] = array(
			'type' => 'mapslocation',
			'aliases' => array( 'center' ),
			'default' => false,
			'manipulatedefault' => false,
		);

		// Give grep a chance to find the usages:
		// maps-par-mappingservice, maps-par-geoservice, maps-par-width,
		// maps-par-height, maps-par-centre
		foreach ( $params as $name => &$data ) {
			$data['name'] = $name;
			$data['message'] = 'maps-par-' . $name;
		}

		return $params;
	}
	
	/**
	 * Resolves the url of images provided as wiki page; leaves others alone.
	 * 
	 * @since 1.0
	 * @deprecated
	 * 
	 * @param string $file
	 * 
	 * @return string
	 */
	public static function getFileUrl( $file ) {
		$title = Title::newFromText( $file, NS_FILE );

		if ( !is_null( $title ) && $title->getNamespace() == NS_FILE && $title->exists() ) {
			$imagePage = new ImagePage( $title );
			$file = $imagePage->getDisplayedFile()->getURL();
		}		
		
		return $file;
	}
	
	/**
	 * Returns JS to init the vars to hold the map data when they are not there already.
	 * 
	 * @since 1.0
	 * @deprecated
	 * 
	 * @param string $serviceName
	 *
	 * @return string
	 */
	public static function getBaseMapJSON( $serviceName ) {
		static $baseInit = false;
		static $serviceInit = array();
		
		$json = '';
		
		if ( !$baseInit ) {
			$baseInit = true;
			$json .= 'var mwmaps={};';
		}
		
		if ( !in_array( $serviceName, $serviceInit ) ) {
			$serviceInit[] = $serviceName;
			$json .= "mwmaps.$serviceName={};";
		}
		
		return $json;
	}
	
}