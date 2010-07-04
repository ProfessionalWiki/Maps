<?php

/**
 * File holding class MapsBasePointMap.
 * 
 * @file Maps_BasePointMap.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Abstract class MapsBasePointMap provides the scafolding for classes handling display_point(s)
 * calls for a specific mapping service. It inherits from MapsMapFeature and therefore forces
 * inheriting classes to implement sereveral methods.
 *
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */
abstract class MapsBasePointMap implements iMapParserFunction {
	
	public $mService;
	
	protected $centreLat, $centreLon;

	protected $output = '';
	
	protected $featureParameters = false;

	protected $markerString;
	
	protected $parser;
	
	private $specificParameters = false;
	private $markerData = array();
	
	protected abstract function getDefaultZoom();	
	
	public function __construct( MapsMappingService $service ) {
		$this->mService = $service;
	}
	
	/**
	 * Sets the map properties as class fields.
	 * 
	 * @param array $mapProperties
	 */
	protected function setMapProperties( array $mapProperties ) {
		foreach ( $mapProperties as $paramName => $paramValue ) {
			if ( !property_exists( __CLASS__, $paramName ) ) {
				$this-> { $paramName } = $paramValue;
			}
			else {
				// If this happens in any way, it could be a big vunerability, so throw an exception.
				throw new Exception( 'Attempt to override a class field during map property assignment. Field name: ' . $paramName );
			}
		}
	}
	
	/**
	 * Returns the specific parameters by first checking if they have been initialized yet,
	 * doing to work if this is not the case, and then returning them.
	 * 
	 * @return array
	 */
	public final function getSpecificParameterInfo() {
		if ( $this->specificParameters === false ) {
			$this->specificParameters = array();
			$this->initSpecificParamInfo( $this->specificParameters );
		}
		
		return $this->specificParameters;
	}
	
	/**
	 * Initializes the specific parameters.
	 * 
	 * Override this method to set parameters specific to a feature service comibination in
	 * the inheriting class.
	 */
	protected function initSpecificParamInfo( array &$parameters ) {
	}	
	
	/**
	 * @return array
	 */
	public function getFeatureParameters() {
		global $egMapsDefaultServices, $egMapsDefaultTitle, $egMapsDefaultLabel, $egMapsMapWidth, $egMapsMapHeight;
		
		return array(
			'width' => array(
				'default' => $egMapsMapWidth
			),
			'height' => array(
				'default' => $egMapsMapHeight
			),			
			'service' => array(
				'default' => $egMapsDefaultServices['display_point']
			),
			'centre' => array(
				'aliases' => array( 'center' ),
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
				'type' => array( 'string', 'list', ';' ),
				'aliases' => array( 'coords', 'location', 'locations', 'address', 'addresses' ),
				'criteria' => array(
					'are_locations' => array( '~' )
				),
				'output-type' => array( 'geoPoints', '~' ),
			),
		);
	}
	
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param Parser $parser
	 * @param array $params
	 * 
	 * @return html
	 */
	public final function getMapHtml( Parser &$parser, array $params ) {
		$this->parser = $parser;
		
		$this->featureParameters = MapsDisplayPoint::$parameters;

		$this->setMapProperties( $params );
		
		$this->setMarkerData();

		$this->createMarkerString();
		
		$this->setCentre();
		
		if ( count( $this->markerData ) <= 1 && $this->zoom == 'null' ) {
			$this->zoom = $this->getDefaultZoom();
		}
		
		$this->addSpecificMapHTML();
		
		$this->mService->addDependencies( $this->parser );
		
		return $this->output;
	}
	
	/**
	 * Fills the $markerData array with the locations and their meta data.
	 */
	private function setMarkerData() {
		$this->title = Xml::escapeJsString( $this->parser->recursiveTagParse( $this->title ) );
		$this->label = Xml::escapeJsString( $this->parser->recursiveTagParse( $this->label ) );
		
		// Each $args is an array containg the coordinate set as first element, possibly followed by meta data. 
		foreach ( $this->coordinates as $args ) {
			$markerData = MapsCoordinateParser::parseCoordinates( array_shift( $args ) );
			
			if ( !$markerData ) continue;
			
			if ( count( $args ) > 0 ) {
				// Parse and add the point specific title if it's present.
				$markerData['title'] = $this->parser->recursiveTagParse( $args[0] );
				
				if ( count( $args ) > 1 ) {
					// Parse and add the point specific label if it's present.
					$markerData['label'] = $this->parser->recursiveTagParse( $args[1] );
					
					if ( count( $args ) > 2 ) {
						// Add the point specific icon if it's present.
						$markerData['icon'] = $args[2];
					}
				}
			}
			
			// If there is no point specific icon, use the general icon parameter when available.
			if ( ! array_key_exists( 'icon', $markerData ) && strlen( $this->icon ) > 0 ) $markerData['icon'] = $this->icon;
			
			// Get the url for the icon when there is one, else set the icon to an empty string.
			if ( array_key_exists( 'icon', $markerData ) ) {
				$icon_image_page = new ImagePage( Title::newFromText( $markerData['icon'] ) );
				$markerData['icon'] = $icon_image_page->getDisplayedFile()->getURL();
			}
			else {
				$markerData['icon'] = '';
			}
			
			$this->markerData[] = $markerData;
		}
	}
	
	/**
	 * Creates a JS string with the marker data. Takes care of escaping the used values.
	 */
	private function createMarkerString() {
		$markerItems = array();
		
		foreach ( $this->markerData as $markerData ) {
			$title = array_key_exists( 'title', $markerData ) ? Xml::escapeJsString( $markerData['title'] ) : $this->title;
			$label = array_key_exists( 'label', $markerData ) ? Xml::escapeJsString( $markerData['label'] ) : $this->label;
			
			$markerData['lon'] = Xml::escapeJsString( $markerData['lon'] );
			$markerData['lat'] = Xml::escapeJsString( $markerData['lat'] );
			$markerData['icon'] = Xml::escapeJsString( $markerData['icon'] );
			
			// TODO: Replace this by by some json_encode stuff.
			$markerItems[] = str_replace(
				array( 'lon', 'lat', 'title', 'label', 'icon' ),
				array( $markerData['lon'], $markerData['lat'], $title, $label, $markerData['icon'] ),
				$this->markerStringFormat
			);
		}
		
		$this->markerString = implode( ',', $markerItems );
	}

	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 */
	private function setCentre() {
		if ( empty( $this->centre ) ) {
			if ( count( $this->markerData ) == 1 ) {
				// If centre is not set and there is exactly one marker, use its coordinates.
				$this->centreLat = Xml::escapeJsString( $this->markerData[0]['lat'] );
				$this->centreLon = Xml::escapeJsString( $this->markerData[0]['lon'] );
			}
			elseif ( count( $this->markerData ) > 1 ) {
				// If centre is not set and there are multiple markers, set the values to null,
				// to be auto determined by the JS of the mapping API.
				$this->centreLat = 'null';
				$this->centreLon = 'null';
			}
			else {
				// If centre is not set and there are no markers, use the default latitude and longitutde.
				$this->setCentreDefaults();
			}
		}
		else { // If a centre value is set, geocode when needed and use it.
			$this->centre = MapsGeocoder::attemptToGeocode( $this->centre, $this->geoservice, $this->mService->getName() );
			
			// If the centre is not false, it will be a valid coordinate, which can be used to set the latitude and longitutde.
			if ( $this->centre ) {
				$this->centreLat = Xml::escapeJsString( $this->centre['lat'] );
				$this->centreLon = Xml::escapeJsString( $this->centre['lon'] );
			}
			else { // If it's false, the coordinate was invalid, or geocoding failed. Either way, the default's should be used.
				$this->setCentreDefaults();
			}
		}
	}
	
	/**
	 * Sets the centre latitude and longitutde to the defaults.
	 */
	private function setCentreDefaults() {
		global $egMapsMapLat, $egMapsMapLon;
		$this->centreLat = $egMapsMapLat;
		$this->centreLon = $egMapsMapLon;
	}
}