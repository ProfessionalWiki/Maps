<?php
/**
 * A query printer for maps using the Google Maps API
 *
 * @file SM_GoogleMaps.php
 * @ingroup SemanticMaps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMGoogleMaps extends SMMapPrinter {
	// TODO: only js that should be printed is a claa to a js function that does all the work

	public function getName() {
		wfLoadExtensionMessages('SemanticMaps');
		return wfMsg('sm_googlemaps_printername');
	}

	protected function getResultText($res, $outputmode) {
		parent::getResultText($res, $outputmode);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item.
		foreach($this->m_params as $paramName => $paramValue) {
			if (empty(${$paramName})) ${$paramName} = $paramValue;
		}
		
		global $wgJsMimeType, $egGoogleMapsKey, $egGoogleMapsOnThisPage, $egMapsGoogleMapsZoom;
		global $wgLang;
		
		$result = "";		
		
		if (strlen($zoom) < 1) $zoom = $egMapsGoogleMapsZoom;
		
		// TODO: autozoom does not work (accuratly) for GE?
		switch($earth) { 
			case 'on' : case 'yes' :
				$earthCode = "map.addMapType(G_SATELLITE_3D_MAP);";
				break;
			default : 
				$earthCode = '';
				break;
		}			
		
		// Get the Google Maps names for the control and map types
		$type = MapsGoogleMaps::getGMapType($type, strlen($earthCode) > 0);
		$control_class = MapsGoogleMaps::getGControlType($controls);

		$map_text = '';
		
		if (empty($egGoogleMapsOnThisPage)) {
			$egGoogleMapsOnThisPage = 0;
			MapsGoogleMaps::addGMapDependencies($map_text);
		}
		$egGoogleMapsOnThisPage++;

		// Enable the scroll wheel zoom when autozoom is not set to off
		switch($autozoom) {
			case 'no' : case 'off' : 
				$autozoomCode = '';
				break;
			default:
				$autozoomCode = 'map.enableScrollWheelZoom();';
				break;
		}
		
		$map_text .= <<<END
<div id="map$egGoogleMapsOnThisPage" class="$class"></div>
<script type="text/javascript">
function makeMap{$egGoogleMapsOnThisPage}() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map$egGoogleMapsOnThisPage"), {size: new GSize('$width', '$height')});
		map.setMapType($type);
		map.addControl(new {$control_class}());
		map.addControl(new GMapTypeControl());
		$autozoomCode $earthCode
END;

		if (count($this->m_locations) > 0) {
			if (empty($centre)) {
				// If the center is not set, it needs to be determined, together with the bounds
				// This is done by extending the bounds with every point (Google Maps API)
				// and then getting the center and zoom level
				$map_text .= "var bounds = new GLatLngBounds();";
				
				foreach ($this->m_locations as $i => $location) {
					// Add the markers to the map
					list($lat, $lon, $title, $label, $icon) = $location;
					$title = str_replace("'", "\'", $title);
					$label = str_replace("'", "\'", $label);
					$map_text .= "
						var point = new GLatLng($lat, $lon);
						bounds.extend(point);
						map.addOverlay(createGMarker(point, '$title', '$label', '$icon'));";
				}
				
				$map_text .= "map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));";
			}
			else {
				if ($centre == null) {
					$centre_lat = 0;
					$centre_lon = 0;
				}
				else {
					// If the center is set, get the coordinates
					// GLatLng class expects only numbers, no letters or degree symbols
					list($centre_lat, $centre_lon) = MapsUtils::getLatLon($centre);
				}
				
				foreach ($this->m_locations as $i => $location) {
					// Add the markers to the map
					list($lat, $lon, $title, $label, $icon) = $location;
					$title = str_replace("'", "\'", $title);
					$label = str_replace("'", "\'", $label);
					$map_text .= "map.addOverlay(createGMarker(new GLatLng($lat, $lon), '$title', '$label', '$icon'));";
				}
				
				$map_text .= "	map.setCenter(new GLatLng($centre_lat, $centre_lon), $zoom);\n";
			}
		}
		
                $map_text .=<<<END
	}
}
addLoadEvent(makeMap{$egGoogleMapsOnThisPage});
</script>
END;

		$result .= $map_text;

		// print further results footer
		// getSearchLabel() method was added in SMW 1.3
		if (method_exists($this, 'getSearchLabel')) {
			$search_label = $this->getSearchLabel(SMW_OUTPUT_HTML);
		} else {
			$search_label = $this->mSearchlabel;
		}
		if ( $this->mInline && $res->hasFurtherResults() && $search_label !== '') {
			$link = $res->getQueryLink();
			$link->setCaption($search_label);
			$result .= "\t<tr class=\"smwfooter\"><td class=\"sortbottom\" colspan=\"" . $res->getColumnCount() . '"> ' . $link->getText($outputmode,$this->mLinker) . "</td></tr>\n";
		}
		return array($result, 'noparse' => 'true', 'isHTML' => 'true');

	}


}

