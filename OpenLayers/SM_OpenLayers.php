<?php
/**
 * A query printer for maps using the Open Layers API
 *
 * @file SM_OpenLayers.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMOpenLayers extends SMMapPrinter {

	public function getName() {
		wfLoadExtensionMessages('SemanticMaps');
		return wfMsg('sm_openlayers_printername');
	}

	protected function getResultText($res, $outputmode) {
		parent::getResultText($res, $outputmode);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item.
		foreach($this->m_params as $paramName => $paramValue) {
			if (empty(${$paramName})) ${$paramName} = $paramValue;
		}
		
		global $egOpenLayersOnThisPage, $egMapsOpenLayersZoom;
		global $wgJsMimeType;
		
		$result = "";
		
		MapsOpenLayers::addOLDependencies($result);
		$egOpenLayersOnThisPage++;
		
		$controlItems = MapsOpenLayers::createControlsString($controls);
		
		$layerItems = MapsOpenLayers::createLayersStringAndLoadDependencies($result, $layers);
		
		$markerItems = '';
		
		if (count($this->m_locations) > 0) {

			foreach ($this->m_locations as $i => $location) {
				// Create a string containing the marker JS 
				list($lat, $lon, $title, $label, $icon) = $location;
				$title = str_replace("'", "\'", $title);
				$label = str_replace("'", "\'", $label);
				$markerItems .= "getOLMarkerData($lon, $lat, '$title', '$label'),";
			}
			
			$markerItems = rtrim($markerItems, ',');
		}	
		
		if (strlen($zoom) < 1) {
			if (count($this->m_locations) > 1) {
				$zoom = 'null';
			}
			else {
				$zoom = $egMapsOpenLayersZoom;
			}
		}
		
		if (strlen($centre) > 0) {
			list($centre_lat, $centre_lon) = MapsUtils::getLatLon($centre);
		}
		else {
			$centre_lat = 'null';
			$centre_lon = 'null';
		}

		$width = $width . 'px';
		$height = $height . 'px';	
			
		$result .= "
		<div id='openlayer_$egOpenLayersOnThisPage' style='width: $width; height: $height; background-color: #cccccc;'></div>
		<script type='$wgJsMimeType'>/*<![CDATA[*/
		initOpenLayer('openlayer_$egOpenLayersOnThisPage', $centre_lon, $centre_lat, $zoom, [$layerItems], [$controlItems],[$markerItems]);
		/*]]>*/</script>";
		
		return array($result, 'noparse' => 'true', 'isHTML' => 'true');
	}


}

