<?php

if (!defined('MEDIAWIKI')) die();

/**
 * Form input hook that adds an Open Layers map format to Semantic Forms
 *
 * @file SM_OpenLayersFormInput.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

final class SMOpenLayersFormInput extends SMFormInput {

	/*
	 * This function is a hook for Semantic Forms, and returns the HTML needed in 
	 * the form to handle coordinate data.
	 */
	public static function formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
		global $wgJsMimeType;
		global $egOpenLayersOnThisPage, $egGoogleMapsOnThisPage, $egMapsOpenLayersZoom;		
		
		SMOpenLayersFormInput::$coordinates = $coordinates;
			
		MapsOpenLayers::addOLDependencies(SMOpenLayersFormInput::$formOutput);
		$egOpenLayersOnThisPage++;
		
		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies(SMYahooMapsFormInput::$formOutput);
		}			
			
		parent::formInputHTML('open_layer', $egOpenLayersOnThisPage, $input_name, $is_mandatory, $is_disabled, $field_args, 'showOLAddress');
		
		if (empty(SMOpenLayersFormInput::$coordinates)) {
			SMOpenLayersFormInput::$zoom = 1;
		} else if (strlen(SMOpenLayersFormInput::$zoom) < 1) {
			 SMOpenLayersFormInput::$zoom = $egMapsOpenLayersZoom;
		}
		
		$controlItems = MapsOpenLayers::createControlsString(SMOpenLayersFormInput::$controls);
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies(SMOpenLayersFormInput::$formOutput, SMOpenLayersFormInput::$mapProperties['layers']);	
		
		$width = SMOpenLayersFormInput::$width . 'px';
		$height = SMOpenLayersFormInput::$height . 'px';			
		
		SMOpenLayersFormInput::$formOutput .="
		<div id='".SMOpenLayersFormInput::$mapName."' style='width: $width; height: $height; background-color: #cccccc;'></div>  
		
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		addLoadEvent(makeFormInputOpenLayer('".SMOpenLayersFormInput::$mapName."', '".SMOpenLayersFormInput::$coordsFieldName."', ".SMOpenLayersFormInput::$centre_lat.", ".SMOpenLayersFormInput::$centre_lon.", ".SMOpenLayersFormInput::$zoom.", ".SMOpenLayersFormInput::$marker_lat.", ".SMOpenLayersFormInput::$marker_lon.", [$layerItems], [$controlItems]));
		/*]]>*/</script>";	
		
		return array(SMOpenLayersFormInput::$formOutput, '');
	}

}
