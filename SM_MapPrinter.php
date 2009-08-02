<?php

/**
 * Abstract class that provides the common functionallity for all map query printers
 *
 * @file SM_MapPrinter.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 * @author Robert Buzink
 * @author Yaron Koren
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

abstract class SMMapPrinter extends SMWResultPrinter {
	// TODO: make class and child's more OOP, in a way similair to class MapsBaseMap in Maps

	/**
	 * Sets the map service specific element name
	 */
	protected abstract function setQueryPrinterSettings();
	
	/**
	 * Map service spesific map count and loading of dependencies
	 */
	protected abstract function doMapServiceLoad();	
	
	/**
	 * Gets the query result
	 */
	protected abstract function addSpecificMapHTML();
	
	protected $m_locations = array();
	
	protected $defaultZoom;
	protected $elementNr;
	protected $elementNamePrefix;
	
	protected $mapName;	
	
	protected $centre_lat;
	protected $centre_lon;	
	
	protected $output = '';
	
	/**
	 * 
	 *
	 */
	public final function getResultText($res, $outputmode) {
		$this->formatResultData($res, $outputmode);
		
		$this->manageMapProperties();	
		
		$this->setQueryPrinterSettings();
		
		$this->doMapServiceLoad();

		$this->mapName = $this->elementNamePrefix.'_'.$this->elementNr;
		
		$this->autozoom = ($this->autozoom == 'no' || $this->autozoom == 'off') ? 'false' : 'true';	
		
		$this->setZoom();
		
		$this->setCentre();		
		
		$this->addSpecificMapHTML();
		
		return array($this->output, 'noparse' => 'true', 'isHTML' => 'true');
	}
	
	public final function getResult($results, $params, $outputmode) {
		// Skip checks, results with 0 entries are normal
		$this->readParameters($params, $outputmode);
		return $this->getResultText($results, SMW_OUTPUT_HTML);
	}
	
	private function formatResultData($res, $outputmode) {
		while ( ($row = $res->getNext()) !== false ) {
			$this->addResultRow($outputmode, $row);
		}
	}	
	
	/**
	 * This function will loop through all properties (fields) of one record (row),
	 * and add the location data, title, label and icon to the m_locations array.
	 *
	 * @param unknown_type $outputmode
	 * @param unknown_type $row The record you want to add data from
	 */
	private function addResultRow($outputmode, $row) {
		global $wgUser;
		$skin = $wgUser->getSkin();		
		
		$title = '';
		$text = '';
		$lat = '';
		$lon = '';		
		
		// Loop throught all fields of the record
		foreach ($row as $i => $field) {
			$pr = $field->getPrintRequest();
			
			// Loop throught all the parts of the field value
			while ( ($object = $field->getNextObject()) !== false ) {
				if ($object->getTypeID() == '_wpg' && $i == 0) {
					$title = $object->getLongText($outputmode, $skin);
				}
				
				if ($object->getTypeID() != '_geo' && $i != 0) {
					$text .= $pr->getHTMLText($skin) . ": " . $object->getLongText($outputmode, $skin) . "<br />";
				}
		
				if ($pr->getMode() == SMWPrintRequest::PRINT_PROP && $pr->getTypeID() == '_geo') {
					list($lat,$lon) = explode(',', $object->getXSDValue());
				}
			}
		}
		
		if (strlen($lat) > 0 && strlen($lon)  > 0) {
			$icon = $this->getLocationIcon($row);
			$this->m_locations[] = array($lat, $lon, $title, $text, $icon);
		}
	}
	
	/**
	 * Get the icon for a row
	 *
	 * @param unknown_type $row
	 * @return unknown
	 */
	private function getLocationIcon($row) {
		$icon = '';
		$legend_labels = array();
		
		// Look for display_options field, which can be set by Semantic Compound Queries
		if (property_exists($row[0], 'display_options')) {
			if (array_key_exists('icon', $row[0]->display_options)) {
				$icon = $row[0]->display_options['icon'];

				// This is somewhat of a hack - if a legend label has been set, we're getting it for every point, instead of just once per icon	
				if (array_key_exists('legend label', $row[0]->display_options)) {
									
					$legend_label = $row[0]->display_options['legend label'];
					
					if (! array_key_exists($icon, $legend_labels)) {
						$legend_labels[$icon] = $legend_label;
					}
				}
			}
		// Icon can be set even for regular, non-compound queries If it is, though, we have to translate the name into a URL here	
		} elseif (array_key_exists('icon', $this->m_params)) {
	
			$icon_title = Title::newFromText($this->m_params['icon']);
			$icon_image_page = new ImagePage($icon_title);
			$icon = $icon_image_page->getDisplayedFile()->getURL();
		}	

		return $icon;
	}
	
	private function manageMapProperties() {
		$this->m_params = MapsMapper::setDefaultParValues($this->m_params, true);
		
		// Go through the array with map parameters and create new variables
		// with the name of the key and value of the item if they don't exist on class level yet.
		foreach($this->m_params as $paramName => $paramValue) {
			if (!property_exists('SMMapPrinter', $paramName)) {
				$this->{$paramName} = $paramValue;
			}
		}
	}	
	
	/**
	 * Sets the zoom level to the provided value, or when not set, to the default.
	 *
	 */
	private function setZoom() {
		if (strlen($this->zoom) < 1) {
			if (count($this->m_locations) > 1) {
		        $this->zoom = 'null';
		    }
		    else {
		        $this->zoom = $this->defaultZoom;
		    }
		}		
	}	
	
	/**
	 * Sets the $centre_lat and $centre_lon fields.
	 * Note: this needs to be done AFTRE the maker coordinates are set.
	 *
	 */
	private function setCentre() {
		if (strlen($this->centre) > 0) {
			list($this->centre_lat, $this->centre_lon) = MapsUtils::getLatLon($this->centre);
		}
		else {
			$this->centre_lat = 'null';
			$this->centre_lon = 'null';
		}		
	}	
	
}
