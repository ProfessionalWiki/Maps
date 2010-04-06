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

abstract class SMFormInput extends MapsMapFeature {

	/**
	 * Determine if geocoding will be enabled and load the required dependencies.
	 */
	protected abstract function manageGeocoding();
	
	/**
	 * Ensures all dependencies for the used map are loaded, and increases that map service's count
	 */
	protected abstract function addFormDependencies();
	
	protected $marker_lat;
	protected $marker_lon;

	protected $earthZoom;
	
	protected $showAddresFunction;
	
	protected $enableGeocoding = false;
	
	private $startingCoords = '';
	
	private $coordinates;
	
	/**
	 * This function is a hook for Semantic Forms, and returns the HTML needed in 
	 * the form to handle coordinate data.
	 * 
	 * @return array
	 */
	public final function formInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args ) {
		global $wgParser, $sfgTabIndex;
		// TODO: Use function args for sf stuffz

		$this->coordinates = $coordinates;
		
		$this->setMapSettings();
		
		$this->featureParameters = SMFormInputs::$parameters;
		
		$this->doMapServiceLoad();
		
		$this->manageGeocoding();

		if ( parent::manageMapProperties( $field_args, __CLASS__ ) ) {
			$this->setCoordinates();
			$this->setCentre();
			$this->setZoom();
			
			// Create html element names.
			$this->setMapName();
			$this->mapName .= '_' . $sfgTabIndex;
			$this->geocodeFieldName = $this->elementNamePrefix . '_geocode_' . $this->elementNr . '_' . $sfgTabIndex;
			$this->coordsFieldName = $this->elementNamePrefix . '_coords_' . $this->elementNr . '_' . $sfgTabIndex;
			$this->infoFieldName = $this->elementNamePrefix . '_info_' . $this->elementNr . '_' . $sfgTabIndex;
	
			// Create the non specific form HTML.
			$this->output .= "
			<input id='" . $this->coordsFieldName . "' name='$input_name' type='text' value='$this->startingCoords' size='40' tabindex='$sfgTabIndex'>
			<span id='" . $this->infoFieldName . "' class='error_message'></span>";
			
			if ( $this->enableGeocoding ) $this->addGeocodingField();
			
			$this->addSpecificMapHTML( $wgParser );
		}
		
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
		
		// Retrieve language values.
		$enter_address_here_text = Xml::escapeJsString( wfMsg( 'semanticmaps_enteraddresshere' ) );
		$lookup_coordinates_text = Xml::escapeJsString( wfMsg( 'semanticmaps_lookupcoordinates' ) );
		$not_found_text = Xml::escapeJsString( wfMsg( 'semanticmaps_notfound' ) );
		
		$adress_field = SMFormInput::getDynamicInput( $this->geocodeFieldName, $enter_address_here_text, 'size="30" name="geocode" style="color: #707070" tabindex="' . $sfgTabIndex . '"' );
		$this->output .= "
		<p>
			$adress_field
			<input type='submit' onClick=\"$this->showAddresFunction(document.forms['createbox'].$this->geocodeFieldName.value, '$this->mapName', '$this->coordsFieldName', '$not_found_text'); return false\" value='$lookup_coordinates_text' />
		</p>";
	}
	
	/**
     * Sets the zoom so the whole map is visible in case there is no maker yet,
     * and sets it to the default when there is a marker but no zoom parameter.
	 */
	private function setZoom() {
        if ( empty( $this->coordinates ) ) {
            $this->zoom = $this->earthZoom;
        } else if ( strlen( $this->zoom ) < 1 ) {
             $this->zoom = $this->defaultZoom;
        }
	}
	
	/**
	 * 
	 * @param $decimal
	 * @return unknown_type
	 */
	private static function latDecimal2Degree( $decimal ) {
		$deg = Maps_GEO_DEG;
		if ( $decimal < 0 ) {
			return abs ( $decimal ) . "$deg S";
		} else {
			return $decimal . "$deg N";
		}
	}
	
	/**
	 * 
	 * @param $decimal
	 * @return unknown_type
	 */
	private static function lonDecimal2Degree( $decimal ) {
		if ( $decimal < 0 ) {
			return abs ( $decimal ) . "° W";
		} else {
			return $decimal . "° E";
		}
	}	
	
	/**
	 * Sets the $marler_lon and $marler_lat fields and when set, the starting coordinates
	 */
	private function setCoordinates() {
		if ( empty( $this->coordinates ) ) {
			// If no coordinates exist yet, no marker should be displayed
			$this->marker_lat = 'null';
			$this->marker_lon = 'null';
		}
		else {
			$marker = MapsCoordinateParser::parseCoordinates( $this->coordinates );
			$this->marker_lat = $marker['lat'];
			$this->marker_lon = $marker['lon'];
			$this->startingCoords =  self::latDecimal2Degree( $this->marker_lat ) . ', ' . self::lonDecimal2Degree( $this->marker_lon );
		}
	}
	
	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 */
	private function setCentre() {
		if ( empty( $this->centre ) ) {
			if ( isset( $this->coordinates ) ) {
				$this->centre_lat = $this->marker_lat;
				$this->centre_lon = $this->marker_lon;
			}
			else {
				$this->centre_lat = '0';
				$this->centre_lon = '0';
			}
		}
		else {
			// Geocode and convert if required.
			$centre = MapsGeocoder::attemptToGeocode( $this->centre, $this->geoservice, $this->serviceName );
			
			$this->centre_lat = Xml::escapeJsString( $centre['lat'] );
			$this->centre_lon = Xml::escapeJsString( $centre['lon'] );
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

