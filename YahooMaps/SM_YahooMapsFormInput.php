<?php

if (!defined('MEDIAWIKI')) die();

/**
* Form input hook that adds an Yahoo! Maps map format to Semantic Forms
 *
 * @file SM_YahooMapsFormInput.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

final class SMYahooMapsFormInput extends SMFormInput {

	/*
	 * This function is a hook for Semantic Forms, and returns the HTML needed in 
	 * the form to handle coordinate data.
	 */
	public static function formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
		global $wgJsMimeType;
		global $egYahooMapsOnThisPage, $egGoogleMapsOnThisPage, $egMapsYahooMapsZoom;		
		
		SMYahooMapsFormInput::$coordinates = $coordinates;
			
		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies(SMYahooMapsFormInput::$formOutput);
		}		
		
		if (empty($egYahooMapsOnThisPage)) {
			$egYahooMapsOnThisPage = 0;
			MapsYahooMaps::addYMapDependencies(SMYahooMapsFormInput::$formOutput);
		}
		$egYahooMapsOnThisPage++;		
		
		parent::formInputHTML('map_yahoo', $egYahooMapsOnThisPage, $input_name, $is_mandatory, $is_disabled, $field_args, 'showYAddress');
		
		if (empty(SMYahooMapsFormInput::$coordinates)) {
			SMYahooMapsFormInput::$zoom = 17;
		} else if (strlen(SMYahooMapsFormInput::$zoom) < 1) {
			 SMYahooMapsFormInput::$zoom = $egMapsYahooMapsZoom;
		}		
		
		$type = MapsYahooMaps::getYMapType(SMYahooMapsFormInput::$type);
		
		$controlItems = MapsYahooMaps::createControlsString(SMGoogleMapsFormInput::$controls);		
		
		$width = SMYahooMapsFormInput::$width . 'px';
		$height = SMYahooMapsFormInput::$height . 'px';		
		
		SMYahooMapsFormInput::$formOutput .="
		<div id='".SMYahooMapsFormInput::$mapName."' style='width: $width; height: $height;'></div>  
		
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputYahooMap('".SMYahooMapsFormInput::$mapName."', '".SMYahooMapsFormInput::$coordsFieldName."', ".SMYahooMapsFormInput::$centre_lat.", ".SMYahooMapsFormInput::$centre_lon.", ".SMYahooMapsFormInput::$zoom.", ".SMYahooMapsFormInput::$marker_lat.", ".SMYahooMapsFormInput::$marker_lon.", $type, [$controlItems], ".SMYahooMapsFormInput::$autozoom."));
		/*]]>*/</script>";

		return array(SMGoogleMapsFormInput::$formOutput, '');		
	}
	
}
