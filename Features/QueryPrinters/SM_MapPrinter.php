<?php

/**
 * File holding abstract class SMMapPrinter.
 *
 * @file SM_MapPrinter.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 * @author Robert Buzink
 * @author Yaron Koren
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Abstract class that provides the common functionallity for all map query printers.
 *
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 * @author Robert Buzink
 * @author Yaron Koren
 * 
 * The adaptor pattern could be used to prevent this.
 */
abstract class SMMapPrinter extends SMWResultPrinter {

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
	protected abstract function addSpecificMapHTML( Parser $parser );
	
	public $serviceName;
	
	protected $m_locations = array();
	
	protected $defaultZoom;
	
	protected $centreLat;
	protected $centreLon;
	
	protected $output = '';
	protected $errorList;
	
	protected $mapFeature;
	
	protected $featureParameters = array();
	protected $spesificParameters = array();
	
	/**
	 * Builds up and returns the HTML for the map, with the queried coordinate data on it.
	 *
	 * @param unknown_type $res
	 * @param unknown_type $outputmode
	 * 
	 * @return array
	 */
	public final function getResultText( $res, $outputmode ) {
		$this->setQueryPrinterSettings();
		
		$this->featureParameters = SMQueryPrinters::$parameters;
		
		if ( self::manageMapProperties( $this->m_params ) ) {
			$this->formatResultData( $res, $outputmode );
			
			// Only create a map when there is at least one result.
			if ( count( $this->m_locations ) > 0 || $this->forceshow ) {
				$this->doMapServiceLoad();
		
				$this->setMapName();
				
				$this->setZoom();
				
				$this->setCentre();
				
				global $wgParser;
				$this->addSpecificMapHTML( $wgParser );
			}
			else {
				// TODO: add warning when level high enough and append to error list?
			}
		}

		return array( $this->output . $this->errorList, 'noparse' => true, 'isHTML' => true );
	}
	
	/**
	 * Validates and corrects the provided map properties, and the sets them as class fields.
	 * 
	 * @param array $mapProperties
	 * 
	 * @return boolean Indicates whether the map should be shown or not.
	 */
	protected final function manageMapProperties( array $mapProperties ) {
		global $egMapsServices;
		
		/*
		 * Assembliy of the allowed parameters and their information. 
		 * The main parameters (the ones that are shared by everything) are overidden
		 * by the feature parameters (the ones spesific to a feature). The result is then
		 * again overidden by the service parameters (the ones spesific to the service),
		 * and finally by the spesific parameters (the ones spesific to a service-feature combination).
		 */
		$parameterInfo = array_merge_recursive( MapsMapper::getCommonParameters(), $this->featureParameters );
		$parameterInfo = array_merge_recursive( $parameterInfo, $egMapsServices[$this->serviceName]['parameters'] );
		$parameterInfo = array_merge_recursive( $parameterInfo, $this->spesificParameters );
		
		$manager = new ValidatorManager();
		
		$showMap = $manager->manageParameters( $mapProperties, $parameterInfo );
		
		if ( $showMap ) {
			$this->setMapProperties( $manager->getParameters( false ), __CLASS__ );
		}
		
		$this->errorList  = $manager->getErrorList();
		
		return $showMap;
	}
	
	/**
	 * Sets the map properties as class fields.
	 * 
	 * @param array $mapProperties
	 * @param string $className
	 */
	private function setMapProperties( array $mapProperties, $className ) {
		foreach ( $mapProperties as $paramName => $paramValue ) {
			if ( ! property_exists( $className, $paramName ) ) {
				$this-> { $paramName } = $paramValue;
			}
			else {
				throw new Exception( 'Attempt to override a class field during map propertie assignment. Field name: ' . $paramName );
			}
		}
	}
	
	public final function getResult( $results, $params, $outputmode ) {
		// Skip checks, results with 0 entries are normal
		$this->readParameters( $params, $outputmode );
		
		return $this->getResultText( $results, SMW_OUTPUT_HTML );
	}
	
	private function formatResultData( $res, $outputmode ) {
		while ( ( $row = $res->getNext() ) !== false ) {
			$this->addResultRow( $outputmode, $row );
		}
	}
	
	/**
	 * This function will loop through all properties (fields) of one record (row),
	 * and add the location data, title, label and icon to the m_locations array.
	 *
	 * @param unknown_type $outputmode
	 * @param unknown_type $row The record you want to add data from
	 */
	private function addResultRow( $outputmode, $row ) {
		global $wgUser, $smgUseSpatialExtensions;
		
		$skin = $wgUser->getSkin();
		
		$title = '';
		$titleForTemplate = '';
		$text = '';
		$lat = '';
		$lon = '';
		
		$coords = array();
		$label = array();
		
		// Loop throught all fields of the record
		foreach ( $row as $i => $field ) {
			$pr = $field->getPrintRequest();
			
			// Loop throught all the parts of the field value
			while ( ( $object = $field->getNextObject() ) !== false ) {
				if ( $object->getTypeID() == '_wpg' && $i == 0 ) {
					if ( $this->showtitle ) $title = $object->getLongText( $outputmode, $skin );
					if ( $this->template ) $titleForTemplate = $object->getLongText( $outputmode, NULL );
				}
				
				if ( $object->getTypeID() != '_geo' && $i != 0 ) {
					if ( $this->template ) {
						if ( $object instanceof SMWWikiPageValue ) {
							$label[] = $object->getTitle()->getPrefixedText();
						} else {
							$label[] = $object->getLongText( $outputmode, $skin );
						}
					}
					else {
						$text .= $pr->getHTMLText( $skin ) . ': ' . $object->getLongText( $outputmode, $skin ) . '<br />';
					}
				}
		
				if ( $pr->getMode() == SMWPrintRequest::PRINT_PROP && $pr->getTypeID() == '_geo' ) {
					$coords[] = $object->getDBkeys();
				}
			}
		}
		
		foreach ( $coords as $coord ) {
			if ( count( $coord ) >= 2 ) {
				if ( $smgUseSpatialExtensions ) {
					// TODO
				}
				else {
					list( $lat, $lon ) = $coord; 
				}
				
				if ( $lat != '' && $lon != '' ) {
					$icon = $this->getLocationIcon( $row );

					if ( $this->template ) {
						global $wgParser;
						
						$segments = array_merge(
							array( $this->template, 'title=' . $titleForTemplate, 'latitude=' . $lat, 'longitude=' . $lon ),
							$label
						);
						
						$text = preg_replace( '/\n+/m', '<br />', $wgParser->recursiveTagParse( '{{' . implode( '|', $segments ) . '}}' ) );
					}

					$this->m_locations[] = array(
						Xml::escapeJsString( $lat ),
						Xml::escapeJsString( $lon ),
						Xml::escapeJsString( $title ),
						Xml::escapeJsString( $text ),
						Xml::escapeJsString( $icon )
					);
				}
			}
		}
	}

	/**
	 * Get the icon for a row
	 *
	 * @param unknown_type $row
	 * @return unknown
	 */
	private function getLocationIcon( $row ) {
		$icon = '';
		$legend_labels = array();
		
		// Look for display_options field, which can be set by Semantic Compound Queries
        // the location of this field changed in SMW 1.5
		$display_location = method_exists( $row[0], 'getResultSubject' ) ? $display_location = $row[0]->getResultSubject() : $row[0];
		
		if ( property_exists( $display_location, 'display_options' ) && is_array( $display_location->display_options ) ) {
			$display_options = $display_location->display_options;
			if ( array_key_exists( 'icon', $display_options ) ) {
				$icon = $display_options['icon'];

				// This is somewhat of a hack - if a legend label has been set, we're getting it for every point, instead of just once per icon	
				if ( array_key_exists( 'legend label', $display_options ) ) {
									
					$legend_label = $display_options['legend label'];
					
					if ( ! array_key_exists( $icon, $legend_labels ) ) {
						$legend_labels[$icon] = $legend_label;
					}
				}
			}
		} // Icon can be set even for regular, non-compound queries If it is, though, we have to translate the name into a URL here
		elseif ( $this->icon != '' ) {
			$icon_image_page = new ImagePage( Title::newFromText( $this->icon ) );
			$icon = $icon_image_page->getDisplayedFile()->getURL();
		}

		return $icon;
	}
	
	/**
	 * Sets the zoom level to the provided value, or when not set, to the default.
	 *
	 */
	private function setZoom() {
		if ( strlen( $this->zoom ) < 1 ) {
			if ( count( $this->m_locations ) > 1 ) {
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
		// If a centre value is set, use it.
		if ( strlen( $this->centre ) > 0 ) {
			// Geocode and convert if required.
			$centre = MapsGeocoder::attemptToGeocode( $this->centre, $this->geoservice, $this->serviceName );
			
			$this->centreLat = $centre['lat'];
			$this->centreLon = $centre['lon'];
		}
		elseif ( count( $this->m_locations ) > 1 ) {
			// If centre is not set, and there are multiple points, set the values to null, to be auto determined by the JS of the mapping API.			
			$this->centreLat = 'null';
			$this->centreLon = 'null';
		}
		elseif ( count( $this->m_locations ) == 1 ) {
			// If centre is not set and there is exactelly one marker, use it's coordinates.			
			$this->centreLat = Xml::escapeJsString( $this->m_locations[0][0] );
			$this->centreLon = Xml::escapeJsString( $this->m_locations[0][1] );
		}
		else {
			// If centre is not set and there are no results, centre on the default coordinates.
			global $egMapsMapLat, $egMapsMapLon;
			
			$this->centreLat = $egMapsMapLat;
			$this->centreLon = $egMapsMapLon;
		}
	}
	
	/**
	 * Sets the $mapName field, using the $elementNamePrefix and $elementNr.
	 *
	 */
	protected function setMapName() {
		$this->mapName = $this->elementNamePrefix . '_' . $this->elementNr;
	}
	
	public final function getName() {
		return wfMsg( 'maps_' . $this->serviceName );
	}
	
    public function getParameters() {
    	global $egMapsMapWidth, $egMapsMapHeight;
    	
        $params = parent::exportFormatParameters();
        
        $params[] = array( 'name' => 'zoom', 'type' => 'int', 'description' => wfMsg( 'semanticmaps_paramdesc_zoom' ) );
        $params[] = array( 'name' => 'width', 'type' => 'int', 'description' => wfMsgExt( 'semanticmaps_paramdesc_width', 'parsemag', $egMapsMapWidth ) );
        $params[] = array( 'name' => 'height', 'type' => 'int', 'description' => wfMsgExt( 'semanticmaps_paramdesc_height', 'parsemag', $egMapsMapHeight ) );

        return $params;
    }
	
}