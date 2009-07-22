<?php

/**
 * A class that holds static helper functions and extension hooks for the Google Maps service
 *
 * @file SM_GoogleMapsFormInput.php
 * @ingroup SemanticMaps
 * 
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMapsFormInput extends SMFormInput {

	/**
	 * This function is a hook for Semantic Forms, and returns the HTML needed in 
	 * the form to handle coordinate data.
	 */
	public static function formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
		global $wgJsMimeType;
		global $egGoogleMapsOnThisPage, $egMapsGoogleMapsZoom;		
		
		SMGoogleMapsFormInput::$coordinates = $coordinates;
			
		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies(SMGoogleMapsFormInput::$formOutput);
		}
		$egGoogleMapsOnThisPage++;		
		
		parent::formInputHTML('map_google', $egGoogleMapsOnThisPage, $input_name, $is_mandatory, $is_disabled, $field_args, 'showGAddress');
		
		
		if (empty(SMGoogleMapsFormInput::$coordinates)) {
			SMGoogleMapsFormInput::$zoom = 1;
		} else if (strlen(SMGoogleMapsFormInput::$zoom) < 1) {
			 SMGoogleMapsFormInput::$zoom = $egMapsGoogleMapsZoom;
		}
		
		$enableEarth = SMGoogleMapsFormInput::$mapProperties['earth'] == 'on' || SMGoogleMapsFormInput::$mapProperties['earth'] == 'yes';
		$earth = $enableEarth ? 'true' : 'false';
		
		SMGoogleMapsFormInput::$type = MapsGoogleMaps::getGMapType(SMGoogleMapsFormInput::$type, $enableEarth);
		$control = MapsGoogleMaps::getGControlType(SMGoogleMapsFormInput::$controls);		
		
		SMGoogleMapsFormInput::$formOutput .= "
		<div id='".SMGoogleMapsFormInput::$mapName."' class='".SMGoogleMapsFormInput::$class."'></div>
	
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputGoogleMap('".SMGoogleMapsFormInput::$mapName."', '".SMGoogleMapsFormInput::$coordsFieldName."', ".SMGoogleMapsFormInput::$width.", ".SMGoogleMapsFormInput::$height.", ".SMGoogleMapsFormInput::$centre_lat.", ".SMGoogleMapsFormInput::$centre_lon.", ".SMGoogleMapsFormInput::$zoom.", ".SMGoogleMapsFormInput::$marker_lat.", ".SMGoogleMapsFormInput::$marker_lon.", ".SMGoogleMapsFormInput::$type.", new $control(), ".SMGoogleMapsFormInput::$autozoom.", $earth));
		window.unload = GUnload;
		/*]]>*/</script>";

		return array(SMGoogleMapsFormInput::$formOutput, '');
	}
	
}
