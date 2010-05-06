<?php

/**
 * Abstract class that provides the common functionallity for all map form inputs
 *
 * @file SM_FormInput.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

abstract class SMFormInput {

	/**
	 * Determine if geocoding will be enabled and load the required dependencies.
	 */
	protected abstract function manageGeocoding();
	
	/**
	 * Ensures all dependencies for the used map are loaded, and increases that map service's count
	 */
	protected abstract function addFormDependencies();
	
	/**
	 * @var string
	 */	
	protected $mapName;
	
	/**
	 * @var array
	 */
	protected $markerCoords;

	protected $earthZoom;
	
	protected $showAddresFunction;
	
	protected $enableGeocoding = false;
	
	private $coordinates;
	
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
		 * by the feature parameters (the ones spesific to a feature). The result is then
		 * again overidden by the service parameters (the ones spesific to the service),
		 * and finally by the spesific parameters (the ones spesific to a service-feature combination).
		 */
		$parameterInfo = array_merge_recursive( MapsMapper::getCommonParameters(), SMFormInputs::$parameters );
		$parameterInfo = array_merge_recursive( $parameterInfo, $egMapsServices[$this->serviceName]['parameters'] );
		$parameterInfo = array_merge_recursive( $parameterInfo, $this->spesificParameters );
		
		$manager = new ValidatorManager();
		
		$showMap = $manager->manageParameters( $mapProperties, $parameterInfo );
		
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
		
		$this->setMapSettings();
		
		$showInput = $this->setMapProperties( $field_args );
		
		if ( !$showInput ) {
			return array( $this->errorList );
		}
		
		$this->doMapServiceLoad();
		
		$this->manageGeocoding();

		$this->setCoordinates();
		$this->setCentre();
		$this->setZoom();
		
		// Create html element names.
		$this->geocodeFieldName = $this->elementNamePrefix . '_geocode_' . $this->elementNr . '_' . $sfgTabIndex;
		$this->coordsFieldName = $this->elementNamePrefix . '_coords_' . $this->elementNr . '_' . $sfgTabIndex;
		$this->infoFieldName = $this->elementNamePrefix . '_info_' . $this->elementNr . '_' . $sfgTabIndex;

		// Create the non specific form HTML.
		$this->output .= Html::input( 
			$input_name,
			$this->markerCoords ? MapsCoordinateParser::formatCoordinates( $this->markerCoords ) : '',
			'text',
			array(
				'size' => 42,
				'tabindex' => $sfgTabIndex,
				'id' => $this->coordsFieldName
			)
		);
		
		$this->output .= Html::element( 
			'span',
			array(
				'class' => 'error_message',
				'id' => $this->infoFieldName
			)
		);
		
		if ( $this->enableGeocoding ) $this->addGeocodingField();
		
		if ( $this->markerCoords === false ) {
			$this->markerCoords = array(
				'lat' => 'null',
				'lon' => 'null'
			);
			$this->centreLat = 'null';
			$this->centreLon = 'null';
		}
		
		$this->addSpecificMapHTML();
		
		return array( $this->output . $this->errorList, '' );
	}
	
	private function addGeocodingField() {
		global $sfgTabIndex, $wgOut, $smgAddedFormJs;
		$sfgTabIndex++;
		
		if ( !$smgAddedFormJs ) {
			$smgAddedFormJs = true;
			
			$n = Xml::escapeJsString( wfMsgForContent( 'maps-abb-north' ) );
			$e = Xml::escapeJsString( wfMsgForContent( 'maps-abb-east' ) );
			$s = Xml::escapeJsString( wfMsgForContent( 'maps-abb-south' ) );
			$w = Xml::escapeJsString( wfMsgForContent( 'maps-abb-south' ) );
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
		
		$adress_field = SMFormInput::getDynamicInput(
			$this->geocodeFieldName,
			wfMsg( 'semanticmaps_enteraddresshere' ),
			'size="30" name="geocode" style="color: #707070" tabindex="' . htmlspecialchars( $sfgTabIndex ) . '"'
		);
		
		$notFoundText = Xml::escapeJsString( wfMsg( 'semanticmaps_notfound' ) );
		$mapName = Xml::escapeJsString( $this->mapName );
		$geoFieldName = Xml::escapeJsString( $this->geocodeFieldName );
		$coordFieldName = Xml::escapeJsString( $this->coordsFieldName );
		
		$this->output .= '<p>' . $adress_field .
			Html::input(
				'geosubmit',
				wfMsg( 'semanticmaps_lookupcoordinates' ),
				'submit',
				array(
					'onClick' => "$this->showAddresFunction( document.forms['createbox'].$geoFieldName.value, '$mapName', '$coordFieldName', '$notFoundText'); return false"
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
            $this->zoom = $this->earthZoom;
        } else if ( $this->zoom == 'null' ) {
             $this->zoom = $this->defaultZoom;
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
	 * @author Jeroen De Dauw
	 *
	 * @param string $id
	 * @param string $value
	 * @param string $args
	 * 
	 * @return html
	 */
	private static function getDynamicInput( $id, $value, $args = '' ) {
		return '<input id="' . $id . '" ' . $args . ' value="' . $value . '" onfocus="if (this.value==\'' . $value . '\') {this.value=\'\';}" onblur="if (this.value==\'\') {this.value=\'' . $value . '\';}" />';
	}
}

