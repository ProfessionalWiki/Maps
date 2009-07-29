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
	// TODO: make class weakly typed, like MapsBaseMap in Maps
	
	/**
	 * Set the map service specific element name and the javascript function handling the displaying of an address
	 */
	protected abstract function setFormInputSettings();
	
	/**
	 * Map service spesific map count and loading of dependencies
	 */
	protected abstract function doMapServiceLoad();
	
	/**
	 * Adds the HTML specific to the mapping service to the output
	 */
	protected abstract function addSpecificFormInputHTML();
	
	/**
	 * Detrmine if geocoding will be enabled and load the required dependencies.
	 */	
	protected abstract function manageGeocoding();
	
	protected $formOutput = '';
	
	protected $coordinates;	
	protected $width;
	protected $height;
	protected $zoom;
	protected $controls;
	protected $type;
	protected $autozoom;
	protected $centre;
	protected $class;
	
	protected $mapName;
	protected $geocodeFieldName;
	protected $coordsFieldName;
	protected $infoFieldName;
	
	protected $mapProperties = array();
	
	protected $marker_lat;
	protected $marker_lon;
	protected $centre_lat;
	protected $centre_lon;
	
	protected $defaultZoom;
	protected $earthZoom;
	
	protected $elementNr;
	protected $elementNamePrefix;
	protected $showAddresFunction;
	
	protected $enableGeocoding = false;
	
	private $startingCoords ='';
	
	/**
	 * This function is a hook for Semantic Forms, and returns the HTML needed in 
	 * the form to handle coordinate data.
	 */
	public final function formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
		global $sfgTabIndex;
		
		$this->coordinates = $coordinates;
		
		$this->manageGeocoding();
		
		$this->setFormInputSettings();
		
		$this->doMapServiceLoad();

		$this->manageMapProperties($field_args);

		$this->setCoordinates();
		$this->setCentre();	
		$this->setZoom();
		
		$this->autozoom = ($this->autozoom == 'no' || $this->autozoom == 'off' ? 'false' : 'true');		
		
		// Create html element names
		$this->mapName = $this->elementNamePrefix.'_'.$this->elementNr;
		$this->geocodeFieldName = $this->elementNamePrefix.'_geocode_'.$this->elementNr;
		$this->coordsFieldName = $this->elementNamePrefix.'_coords_'.$this->elementNr;
		$this->infoFieldName = $this->elementNamePrefix.'_info_'.$this->elementNr;			

		// Create the non specific form HTML
		$this->formOutput .= "
		<input id='".$this->coordsFieldName."' name='$input_name' type='text' value='$this->startingCoords' size='40' tabindex='$sfgTabIndex'>
		<span id='".$this->infoFieldName."' class='error_message'></span>";
		
		if ($this->enableGeocoding) {
			$sfgTabIndex++;
			
			// Retrieve language values
			wfLoadExtensionMessages( 'SemanticMaps' );
			$enter_address_here_text = wfMsg('semanticmaps_enteraddresshere');
			$lookup_coordinates_text = wfMsg('semanticmaps_lookupcoordinates');	
			$not_found_text = wfMsg('semanticmaps_notfound');				
			
			$adress_field = smfGetDynamicInput($this->geocodeFieldName, $enter_address_here_text, 'size="30" name="geocode" style="color: #707070" tabindex="'.$sfgTabIndex.'"');
			$this->formOutput .= "
			<p>
				$adress_field
				<input type='submit' onClick=\"$this->showAddresFunction(document.forms['createbox'].$this->geocodeFieldName.value, '$this->mapName', '$this->coordsFieldName', '$not_found_text'); return false\" value='$lookup_coordinates_text' />
			</p>";
		}
		
		$this->addSpecificFormInputHTML();
		
		return array($this->formOutput, '');
	}
	
	/**
	 * Sets both the common map properties to their defaults or the provided values
	 * and adds non common properties to the $mapProperties field.
	 *
	 * @param unknown_type $mapProperties
	 */
	private function manageMapProperties($mapProperties) {
		$mapProperties = MapsMapper::setDefaultParValues($mapProperties, true);

		foreach($mapProperties as $paramName => $paramValue) {
			if (property_exists('SMFormInput', $paramName) && $paramName != 'coordinates') {
				// If the field exists at class level, set the value
				$this->{$paramName} = $paramValue; 
			}
			else {
				// If the field does not exists at class level (so represents a mapping service specific parameter),
				// add the value to the $mapProperties field.
				$this->mapProperties[$paramName] = $paramValue; 
			}
		}		
	}
	
	/**
	 * Sets the zoom so the whole map is visible in case there is no maker yet,
	 * and sets it to the default when there is a marker but no zoom parameter.
	 *
	 * @param unknown_type $defaultZoom The mapping service's default zoom level
	 * @param unknown_type $earthZoom The mapping service's earth-level zoom (on which the complete map is visible)
	 */
	private function setZoom() {
		if (empty($this->coordinates)) {
			$this->zoom = $this->earthZoom;
		} else if (strlen($this->zoom) < 1) {
			 $this->zoom = $this->defaultZoom;
		}		
	}
	
	/**
	 * Sets the $marler_lon and $marler_lat fields and when set, the starting coordinates
	 *
	 */
	private function setCoordinates() {
		if (empty($this->coordinates)) {
			// If no coordinates exist yet, no marker should be displayed
			$this->marker_lat = 'null';
			$this->marker_lon = 'null';
		}
		else {
			list($this->marker_lat, $this->marker_lon) = MapsUtils::getLatLon($this->coordinates);
			$this->startingCoords =  MapsUtils::latDecimal2Degree($this->marker_lat) . ', ' . MapsUtils::lonDecimal2Degree($this->marker_lon);
		}
	}
	
	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 *
	 */
	private function setCentre() {
		if (empty($this->centre)) {
			if (isset($this->coordinates)) {
				$this->centre_lat = $this->marker_lat;
				$this->centre_lon = $this->marker_lon;
			}
			else {
				$this->centre_lat = '0';
				$this->centre_lon = '0';	
			}
		}
		else {
			list($this->centre_lat, $this->centre_lon) = MapsUtils::getLatLon($this->centre);
		}		
	}
}

