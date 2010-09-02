<?php

/**
 * Class for the 'display_point' parser hooks.
 * 
 * @since 0.7
 * 
 * @file Maps_DisplayPoint.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsDisplayPoint extends ParserHook {
	
	/**
	 * No LST in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */
	public static function staticMagic( array &$magicWords, $langCode ) {
		$className = __CLASS__;
		$instance = new $className();
		return $instance->magic( $magicWords, $langCode );
	}
	
	/**
	 * No LST in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */	
	public static function staticInit( Parser &$wgParser ) {
		$className = __CLASS__;
		$instance = new $className();
		return $instance->init( $wgParser );
	}	
	
	public static function initialize() {
		
	}
	
	/**
	 * Formats a set of points that can have meta data provided.
	 * 
	 * @param string $locations
	 * @param string $name The name of the parameter.
	 * @param array $parameters Array containing data about the so far handled parameters.
	 */		
	public static function formatGeoPoints( &$locations, $name, array $parameters, $metaDataSeparator = false ) {
		$locations = (array)$locations;
		foreach ( $locations as &$location ) {
			self::formatGeoPoint( $location, $name, $parameters, $metaDataSeparator );
		}
	}
	
	public static function formatGeoPoint( &$location, $name, array $parameters, $metaDataSeparator = false ) {
		if ( $metaDataSeparator !== false ) {
			$segments = explode( $metaDataSeparator, $location );
		}
		else {
			$segments = array( $location );
		}
		
		MapsMapper::formatLocation( $segments[0], $name, $parameters );
		
		$location = $segments;
	}		
	
	/**
	 * Gets the name of the parser hook.
	 * @see ParserHook::getName
	 * 
	 * @since 0.7
	 * 
	 * @return string
	 */
	protected function getName() {
		return 'display_point';
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * @see ParserHook::getParameterInfo
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getParameterInfo() {
		global $egMapsMapWidth, $egMapsMapHeight, $egMapsDefaultServices, $egMapsDefaultTitle, $egMapsDefaultLabel;
		
		Validator::addOutputFormat( 'geoPoints', array( __CLASS__, 'formatGeoPoints' ) );
		
		return array_merge( MapsMapper::getCommonParameters(), array(
			// TODO
			'width' => array(
				'default' => $egMapsMapWidth
			),
			'height' => array(
				'default' => $egMapsMapHeight
			),			
			'mappingservice' => array(
				'default' => $egMapsDefaultServices['display_point']
			),
			'centre' => array(
				'aliases' => array( 'center' ),
				'tolower' => false,
			),
			'title' => array(
				'default' => $egMapsDefaultTitle
			),
			'label' => array(
				'default' => $egMapsDefaultLabel
			),
			'icon' => array(
				'criteria' => array(
					'not_empty' => array()
				)
			),
			'coordinates' => array(
				'required' => true,
				'tolower' => false,
				'type' => array( 'string', 'list', ';' ),
				'aliases' => array( 'coords', 'location', 'locations', 'address', 'addresses' ),
				'criteria' => array(
					'are_locations' => array( '~' )
				),
				'output-type' => array( 'geoPoints', '~' ),
			),
		) );
	}
	
	/**
	 * Returns the list of default parameters.
	 * @see ParserHook::getDefaultParameters
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getDefaultParameters() {
		return array( 'coordinates' );
	}
	
	/**
	 * Renders and returns the output.
	 * @see ParserHook::render
	 * 
	 * @since 0.7
	 * 
	 * @param array $parameters
	 * 
	 * @return string
	 */
	public function render( array $parameters ) {
		// Get the instance of the service class. 
		$service = MapsMappingServices::getValidServiceInstance( $parameters['mappingservice'], $this->getName() );
		
		// Get an instance of the class handling the current parser hook and service. 
		$mapClass = $service->getFeatureInstance( $this->getName() );
		
		return $mapClass->getMapHtml( $parameters );
		
		if ( $mapClass === false ) {
			return ''; // TODO
		}
		else {
			return ''; // TODO
			//return $mapClass->getMapHtml( $parameters );
		}
	}	
			
}