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

	protected $m_locations = array();
	
	public function getResult($results, $params, $outputmode) {
		// Skip checks, results with 0 entries are normal
		$this->readParameters($params, $outputmode);
		return $this->getResultText($results, SMW_OUTPUT_HTML);
	}
	
	protected function getResultText($res, $outputmode) {
		while ( ($row = $res->getNext()) !== false ) {
			$this->addResultRow($outputmode, $row);
		}
		
		$this->m_params = MapsMapper::setDefaultParValues($this->m_params, true);
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
	
}
