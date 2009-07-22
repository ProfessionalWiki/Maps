<?php

/**
 * Abstract class that provides the common functionallity for all map form inputs
 *
 * @file SM_FormInput.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

abstract class SMFormInput {
	protected static $formOutput = '';
	
	protected static $coordinates;	
	
	protected static $width;
	protected static $height;
	protected static $zoom;
	protected static $controls;
	protected static $type;
	protected static $autozoom;
	protected static $centre;
	protected static $class;
	protected static $service;
	
	protected static $mapName;
	protected static $geocodeFieldName;
	protected static $coordsFieldName;
	protected static $infoFieldName;
	
	protected static $mapProperties = array();
	
	protected static $marker_lat;
	protected static $marker_lon;
	protected static $centre_lat;
	protected static $centre_lon;
	
	public static function formInputHTML($elementNamePrefix, $elementNr, $input_name, $is_mandatory, $is_disabled, $field_args, $showAddresFunction) {
		global $egGoogleMapsKey;
		
		SMFormInput::$mapName = $elementNamePrefix.'_'.$elementNr;
		SMFormInput::$geocodeFieldName = $elementNamePrefix.'_geocode_'.$elementNr;
		SMFormInput::$coordsFieldName = $elementNamePrefix.'_coords_'.$elementNr;
		SMFormInput::$infoFieldName = $elementNamePrefix.'_info_'.$elementNr;		
		
		$mapProperties = MapsMapper::setDefaultParValues($field_args, true);

		foreach($mapProperties as $paramName => $paramValue) {
			if (property_exists('SMFormInput', $paramName) && $paramName != 'coordinates') {
				SMFormInput::${$paramName} = $paramValue;
			}
			else {
				SMFormInput::$mapProperties[$paramName] = $paramValue;
			}
		}
		
		$starting_coords = '';
		
		if (empty(SMFormInput::$coordinates)) {
			// If no coordinates exist yet, no marker should be displayed
			SMFormInput::$marker_lat = 'null';
			SMFormInput::$marker_lon = 'null';
		}
		else {
			list(SMFormInput::$marker_lat, SMFormInput::$marker_lon) = MapsUtils::getLatLon(SMFormInput::$coordinates);
			$starting_coords =  MapsUtils::latDecimal2Degree(SMFormInput::$marker_lat) . ', ' . MapsUtils::lonDecimal2Degree(SMFormInput::$marker_lon);
		}
		
		if (empty(SMFormInput::$centre)) {
			if (isset(SMFormInput::$coordinates)) {
				SMFormInput::$centre_lat = SMFormInput::$marker_lat;
				SMFormInput::$centre_lon = SMFormInput::$marker_lon;
			}
			else {
				SMFormInput::$centre_lat = '0';
				SMFormInput::$centre_lon = '0';	
			}
		}
		else {
			list(SMFormInput::$centre_lat, SMFormInput::$centre_lon) = MapsUtils::getLatLon(SMFormInput::$centre);
		}		
		
		SMFormInput::$autozoom = (SMFormInput::$autozoom == 'no' || SMFormInput::$autozoom == 'off' ? 'false' : 'true');		
		
		// Retrieve language values
		wfLoadExtensionMessages( 'SemanticMaps' );
		$enter_address_here_text = wfMsg('sm_googlemaps_enteraddresshere');
		$lookup_coordinates_text = wfMsg('sm_googlemaps_lookupcoordinates');	
		$not_found_text = wfMsg('sm_googlemaps_notfound');	

		SMFormInput::$formOutput .= "
		<input id='".SMFormInput::$coordsFieldName."' name='$input_name' type='text' value='$starting_coords' size='40'>
		<span id='".SMFormInput::$infoFieldName."' class='error_message'></span>";

		if (strlen($egGoogleMapsKey) > 0) {
			$adress_field = smfGetDynamicInput(SMFormInput::$geocodeFieldName, $enter_address_here_text, 'size="30" name="geocode" style="color: #707070"');
			SMFormInput::$formOutput .= "
			<p>
				$adress_field
				<input type='submit' onClick=\"$showAddresFunction(document.forms['createbox'].".SMFormInput::$geocodeFieldName.".value, '".SMFormInput::$mapName."', '".SMFormInput::$coordsFieldName."', '$not_found_text'); return false\" value='$lookup_coordinates_text' />
			</p>";
		}
	}
}

