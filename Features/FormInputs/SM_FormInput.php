<?php

/**
 * Abstract class that provides the common functionality for all map form inputs
 *
 * @file SM_FormInput.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

abstract class SMFormInput implements iMappingFeature {

	/**
	 * Ensures all dependencies for the used map are loaded, and increases that map service's count
	 */
	protected abstract function addFormDependencies();
	
	/**
	 * Returns the zoom level at which the whole earth is visible.
	 */
	protected abstract function getEarthZoom();	
	
	/**
	 * List of parameter definitions for forms.
	 * 
	 * @var array or false
	 */
	protected static $formParameters = false;
	
	/**
	 * @var iMappingService
	 */
	protected $service;
	
	/**
	 * @var array
	 */
	protected $markerCoords;
	
	/**
	 * @var string
	 */
	protected $errorList;
	
	/**
	 * Parameters specific to this feature.
	 * 
	 * @var mixed
	 */
	protected $specificParameters = false;
	
	protected $coordsFieldName;
	
	private $coordinates;
	
	/**
	 * Constructor.
	 * 
	 * @param iMappingService $service
	 */
	public function __construct( iMappingService $service ) {
		$this->service = $service;
	}
	
	/**
	 * Returns the specific parameters by first checking if they have been initialized yet,
	 * doing to work if this is not the case, and then returning them.
	 * 
	 * @since 0.6.5
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
	 * 
	 * @since 0.6.5
	 * 
	 * @param array $parameters
	 */
	protected function initSpecificParamInfo( array &$parameters ) {
	}	
	
	/**
	 * Validates and corrects the provided map properties, and the sets them as class fields.
	 * 
	 * @param array $mapProperties
	 * 
	 * @return boolean Indicates whether the map should be shown or not.
	 */
	protected final function setMapProperties( array $mapProperties ) {
		global $egMapsServices;
		
		/*
		 * Assembliy of the allowed parameters and their information. 
		 * The main parameters (the ones that are shared by everything) are overidden
		 * by the feature parameters (the ones specific to a feature). The result is then
		 * again overidden by the service parameters (the ones specific to the service),
		 * and finally by the specific parameters (the ones specific to a service-feature combination).
		 */
		$parameterInfo = MapsMapper::getCommonParameters();
		$parameterInfo = array_merge_recursive( $parameterInfo, $this->getFormParameterInfo() );
		$parameterInfo = array_merge_recursive( $parameterInfo, $this->service->getParameterInfo() );
		$parameterInfo = array_merge_recursive( $parameterInfo, $this->getSpecificParameterInfo() );
		
		$manager = new ValidatorManager();

		$showMap = $manager->manageParsedParameters( $mapProperties, $parameterInfo );
		
		if ( $showMap ) {
			$parameters = $manager->getParameters( false );
			
			foreach ( $parameters as $paramName => $paramValue ) {
				if ( !property_exists( __CLASS__, $paramName ) ) {
					$this-> { $paramName } = $paramValue;
				}
				else {
					// If this happens in any way, it could be a big vunerability, so throw an exception.
					throw new Exception( 'Attempt to override a class field during map property assignment. Field name: ' . $paramName );
				}
			}
		}
		
		$this->errorList = $manager->getErrorList();
		
		return $showMap;
	}
	
	/**
	 * This function is a hook for Semantic Forms, and returns the HTML needed in 
	 * the form to handle coordinate data.
	 * 
	 * @return array
	 * 
	 * TODO: Use function args for sf stuffz
	 */
	public final function formInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args ) {
		global $sfgTabIndex;

		$this->coordinates = $coordinates;
		
		$showInput = $this->setMapProperties( $field_args );
		
		if ( !$showInput ) {
			return array( $this->errorList );
		}
		
		$this->setCoordinates();
		$this->setCentre();
		$this->setZoom();
		
		// Create html element names.
		$mapName = $this->service->getMapId();
		$this->coordsFieldName = $mapName . '_coords_' . $sfgTabIndex;
		$infoFieldName = $mapName . '_info_' . $sfgTabIndex;				
		
		$geocodingFunction = $this->getShowAddressFunction(); 

		// Create the non specific form HTML.
		$this->output .= Html::input( 
			$input_name,
			$this->markerCoords ? MapsCoordinateParser::formatCoordinates( $this->markerCoords ) : '',
			'text',
			array(
				'size' => 42, #_O
				'tabindex' => $sfgTabIndex,
				'id' => $this->coordsFieldName
			)
		);
		
		$this->output .= Html::element( 
			'span',
			array(
				'class' => 'error_message',
				'id' => $infoFieldName
			)
		);
		
		if ( $geocodingFunction !== false ) {
			$this->addGeocodingField( $geocodingFunction, $mapName, $mapName . '_geocode_' . $sfgTabIndex );
		}			
		
		if ( $this->markerCoords === false ) {
			$this->markerCoords = array(
				'lat' => 'null',
				'lon' => 'null'
			);
			
			$this->centreLat = 'null';
			$this->centreLon = 'null';
		}
		
		$this->addSpecificMapHTML();
		
		$this->addFormDependencies();
		
		return array( $this->output . $this->errorList, '' );
	}
	
	/**
	 * Adds geocoding controls to the form.
	 * 
	 * @param string $geocodingFunction
	 * @param string $mapName
	 * @param string $geocodeFieldName
	 */
	private function addGeocodingField( $geocodingFunction, $mapName, $geocodeFieldId ) {
		global $sfgTabIndex, $wgOut, $smgAddedFormJs;
		$sfgTabIndex++;
		
		if ( !$smgAddedFormJs ) {
			$smgAddedFormJs = true;
			
			$n = Xml::escapeJsString( wfMsgForContent( 'maps-abb-north' ) );
			$e = Xml::escapeJsString( wfMsgForContent( 'maps-abb-east' ) );
			$s = Xml::escapeJsString( wfMsgForContent( 'maps-abb-south' ) );
			$w = Xml::escapeJsString( wfMsgForContent( 'maps-abb-west' ) );
			$deg = Xml::escapeJsString( Maps_GEO_DEG );
			
			$wgOut->addInlineScript(
					<<<EOT
function convertLatToDMS (val) {
	return Math.abs(val) + "$deg " + ( val < 0 ? "$s" : "$n" );
}
function convertLngToDMS (val) {
	return Math.abs(val) + "$deg " + ( val < 0 ? "$w" : "$e" );
}
EOT
			);
		}
		
		$adressField = SMFormInput::getDynamicInput(
			'geocode',
			wfMsg( 'semanticmaps_enteraddresshere' ),
			array(
				'size' => '30',
				'id' => $geocodeFieldId,
				'style' => 'color: #707070',
				'tabindex' => $sfgTabIndex
			)
		);
		
		$notFoundText = Xml::escapeJsString( wfMsg( 'semanticmaps_notfound' ) );
		$mapName = Xml::escapeJsString( $mapName );
		$geoFieldId = Xml::escapeJsString( $geocodeFieldId );
		$coordFieldName = Xml::escapeJsString( $this->coordsFieldName );
		
		$this->output .= '<p>' . $adressField .
			Html::input(
				'geosubmit',
				wfMsg( 'semanticmaps_lookupcoordinates' ),
				'submit',
				array(
					'onClick' => "$geocodingFunction( document.forms['createbox'].$geoFieldId.value, '$mapName', '$coordFieldName', '$notFoundText'); return false"
				)
			) . 
			'</p>';
	}
	
	/**
     * Sets the zoom so the whole map is visible in case there is no maker yet,
     * and sets it to the default when there is a marker but no zoom parameter.
	 */
	private function setZoom() {
        if ( empty( $this->coordinates ) ) {
            $this->zoom = $this->getEarthZoom();
        } else if ( $this->zoom == 'null' ) {
             $this->zoom = $this->service->getDefaultZoom();
        }
	}
	
	/**
	 * Sets the $this->markerCoords value, which are the coordinates for the marker.
	 */
	private function setCoordinates() {
		if ( empty( $this->coordinates ) ) {
			// If no coordinates exist yet, no marker should be displayed.
			$this->markerCoords = false;
		}
		else {
			$this->markerCoords = MapsCoordinateParser::parseCoordinates( $this->coordinates );
		}
	}
	
	/**
	 * Sets the $centreLat and $centreLon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 */
	private function setCentre() {
		if ( empty( $this->centre ) ) {
			if ( isset( $this->coordinates ) ) {
				$this->centreLat = $this->markerCoords['lat'];
				$this->centreLon = $this->markerCoords['lon'];
			}
			else {
				$this->centreLat = '0';
				$this->centreLon = '0';
			}
		}
		else {
			// Geocode and convert if required.
			$centre = MapsGeocoder::attemptToGeocode( $this->centre, $this->geoservice, $this->serviceName );
			
			$this->centreLat = Xml::escapeJsString( $centre['lat'] );
			$this->centreLon = Xml::escapeJsString( $centre['lon'] );
		}
	}
	
	/**
	 * Returns html for an html input field with a default value that will automatically dissapear when
	 * the user clicks in it, and reappers when the focus on the field is lost and it's still empty.
	 *
	 * @param string $name
	 * @param string $value
	 * @param array $attribs
	 * 
	 * @return string (html)
	 */
	protected static function getDynamicInput( $name, $value, $attribs = array() ) {
		$escapedValue = Xml::escapeJsString( $value );
		
		$attribs['onfocus'] = "if (this.value==\"$escapedValue\") {this.value='';}";
		$attribs['onblur'] = "if (this.value=='') {this.value=\"$escapedValue\";}";
		
		return Html::input(
			$name,
			$value,
			'text',
			$attribs
		);
	}
	
	/**
	 * Returns the name of the JavaScript function to use for live geocoding,
	 * or false to indicate there is no such function. Override this method
	 * to implement geocoding functionallity.
	 * 
	 * @return mixed: string or false
	 */
	protected function getShowAddressFunction() {
		return false;
	}
	
	/**
	 * Gets the definitions for the parameters specific to the form input feature.
	 * This function implements a form of caching by storing the definitions, once
	 * created, in self::$formParameters, and returning that field when set.
	 * 
	 * @since 0.6.5
	 * 
	 * @return array
	 */
	protected function getFormParameterInfo() {
		$parameters = self::$formParameters;
		
		if ( $parameters === false ) {
			$parameters = $this->initializeFormParameters();
			self::$formParameters = $parameters;
		}
		
		return $parameters;
	}	
	
	/**
	 * Initializes and returns the definitions for the parameters specific to the form input feature.
	 * 
	 * @since 0.6.5
	 * 
	 * @return array
	 */
	private static function initializeFormParameters() {
		global $egMapsAvailableServices, $egMapsDefaultServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
		global $smgFIWidth, $smgFIHeight;
		
		return array(
			'width' => array(
				'default' => $smgFIWidth
			),
			'height' => array(
				'default' => $smgFIHeight
			),		
			'centre' => array(
				'aliases' => array( 'center' ),
			),
			'geoservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
			'mappingservice' => array(
				'default' => $egMapsDefaultServices['fi']
			),				
			'service_name' => array(),
			'part_of_multiple' => array(),
			'possible_values' => array(
				'type' => array( 'string', 'array' ),
			),
			'is_list' => array(),
			'semantic_property' => array(),
			'value_labels' => array(),
		);
	}	
		
}