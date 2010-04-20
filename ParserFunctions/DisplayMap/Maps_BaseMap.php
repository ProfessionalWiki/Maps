<?php

/**
 * File holding class MapsBaseMap.
 *
 * @file Maps_BaseMap.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Abstract class MapsBaseMap provides the scafolding for classes handling display_map
 * calls for a spesific mapping service. It inherits from MapsMapFeature and therefore
 * forces inheriting classes to implement sereveral methods.
 *
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */
abstract class MapsBaseMap extends MapsMapFeature implements iDisplayFunction {
	
	/**
	 * @return array
	 */	
	public function getFeatureParameters() {
		global $egMapsDefaultServices;
		
		return array_merge(
			parent::getFeatureParameters(),
			array(
				'service' => array(	
					'default' => $egMapsDefaultServices['display_map']
				),
				'coordinates' => array(
					'required' => true,
					'aliases' => array( 'coords', 'location', 'locations' ),
					'criteria' => array(
						'is_location' => array()
					),
					'output-type' => 'coordinateSet', 
				),					
			)
		);
	}
	
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param unknown_type $parser
	 * @param array $params
	 * 
	 * @return html
	 */
	public final function displayMap( Parser &$parser, array $params ) {
		$this->setMapSettings();
		
		$this->featureParameters = MapsDisplayMap::$parameters;
		
		$this->doMapServiceLoad();

		parent::setMapProperties( $params, __CLASS__ );
		
		$this->setMapName();
		
		$this->setZoom();
		
		$this->setCentre();
		
		$this->addSpecificMapHTML( $parser );
		
		return $this->output;
	}
	
	/**
	 * Sets the zoom level to the provided value. When no zoom is provided, set
	 * it to the default when there is only one location, or the best fitting soom when
	 * there are multiple locations.
	 *
	 */
	private function setZoom() {
		if ( empty( $this->zoom ) ) $this->zoom = $this->defaultZoom;
	}
	
	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 */
	private function setCentre() {
		if ( empty( $this->coordinates ) ) { // If centre is not set, use the default latitude and longitutde.
			global $egMapsMapLat, $egMapsMapLon;
			$this->centreLat = $egMapsMapLat;
			$this->centreLon = $egMapsMapLon;
		}
		else { // If a centre value is set, geocode when needed and use it.
			$this->coordinates = MapsGeocoder::attemptToGeocode( $this->coordinates, $this->geoservice, $this->serviceName );

			// If the centre is not false, it will be a valid coordinate, which can be used to set the  latitude and longitutde.
			if ( $this->coordinates ) {
				$this->centreLat = Xml::escapeJsString( $this->coordinates['lat'] );
				$this->centreLon = Xml::escapeJsString( $this->coordinates['lon'] );
			}
			else { // If it's false, the coordinate was invalid, or geocoding failed. Either way, the default's should be used.
				// TODO: Some warning this failed would be nice here. 
				global $egMapsMapLat, $egMapsMapLon;
				$this->centreLat = $egMapsMapLat;
				$this->centreLon = $egMapsMapLon;
			}
		}
	}
	
}
