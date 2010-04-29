<?php

/**
 * File holding the MapsOSMCgiBin class.
 *
 * @file Maps_OSMCgiBin.php
 * @ingroup MapsOpenStreetMap
 *
 * @author Ævar Arnfjörð Bjarmason
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * A class for static image requests.
 *
 * @author Ævar Arnfjörð Bjarmason
 * @author Jeroen De Dauw
 */

final class MapsOSMCgiBin {
	private $lat;
	private $lon;
	private $zoom;
	private $width;
	private $height;
	private $url;

	/** borrowed from OpenLayers.DOTS_PER_UNIT */
	private static $INCHES_PER_UNIT = array (
	   	'dd' => 4374754,
	   	'm' => 39.3701
	);
    
	/** borrowed from OpenLayers.DOTS_PER_INCH */
	private static $DOTS_PER_INCH = 72;
    
	/** pixel size in meters */
	private static $PIXEL_SIZE = 0.00028;

	/**
	 * Constructor
	 */
	public function __construct( $lat, $lon, $zoom, $width, $height, $lang, $options ) {
		$this->lat = $lat;
		$this->lon = $lon;
		$this->zoom = $zoom;
		$this->width = $width;
		$this->height = $height;
		$this->lang = $lang;
		$this->options = $options;

		self::initResolutionsAndScales();
		self::setBounds();
	}

	/**
	 * Returns the image url.
	 * 
	 * @return string
	 */
	public function getUrl() {
		$urlElements = array();
		
		$urlElements[] = $this->options['base_url'];
		$urlElements[] = '?bbox=';
		$urlElements[] = implode( ',', $this->bounds );
		$urlElements[] = '&amp;scale=';
		$urlElements[] = $this->scale;
		$urlElements[] = '&amp;format=';
		$urlElements[] = $this->options['format'];
		$urlElements[] = '&amp;locale=';
		$urlElements[] = $this->lang;
		
		if ( array_key_exists( 'maptype', $this->options ) ) {
			$urlElements[] = '&amp;maptype=';
			$urlElements[] = $this->options['maptype'];
		}

		return implode( '', $urlElements );
	}

	/**
	 * This sets the map bounds
	 */
	public function setBounds() {

		/* Determine scale and map bounds for static render request */
		$resolution = $this->resolutions[round( $this->zoom )];
		$this->scale = self::getScaleFromResolution( $resolution );

		/*
		 * Calculate width for Mapnik output using a standard pixel size of 0.00028m
		 * @see http://trac.mapnik.org/wiki/ScaleAndPpi
		 */
		$w_deg = $this->width * self::$PIXEL_SIZE * $this->scale;
		$h_deg = $this->height * self::$PIXEL_SIZE * $this->scale;

		$center = array( $this->lon, $this->lat );
		if ( $this->options['sphericalMercator'] ) {
			// Calculate bounds within a spherical mercator projection if that is what the scale is based on.
			$mercatorCenter = self::forwardMercator( $center );
			
			$this->bounds = self::inverseMercator(
				array(
					$mercatorCenter[0] - $w_deg / 2,
					$mercatorCenter[1] - $h_deg / 2,
					$mercatorCenter[0] + $w_deg / 2,
					$mercatorCenter[1] + $h_deg / 2
				)
			);
		}
		else {
			// Calculate bounds within WGS84
			$this->bounds = array(
				$center[0] - $w_deg / 2,
				$center[1] - $h_deg / 2,
				$center[0] + $w_deg / 2,
				$center[1] + $h_deg / 2
			);
		}
	}

	/**
	* Borrowed from OpenLayers.Util.getScaleFromResolution
	*/
	protected function getScaleFromResolution( $resolution ) {
		return $resolution * self::$INCHES_PER_UNIT[$this->options['unit']] * self::$DOTS_PER_INCH;
	}
    
	/**
	* Determines resolutions and scales based on a maximum resolution and number of zoom levels
	* Borrowed from OpenLayers.Layer.initResolutions
	*/
	protected function initResolutionsAndScales() {
       		$this->resolutions = array();
	    	$base = 2;
		
    		for ( $i = 0; $i < $this->options['numZoomLevels']; $i++ ) {
    			$this->resolutions[$i] = $this->options['maxResolution'] / pow( $base, $i );
			$this->scales[$i] = $this->getScaleFromResolution( $this->resolutions[$i] );
		}
	}
	
	/**
	 * Convert from WGS84 to spherical mercator.
	 */
	private static function forwardMercator( array $lonlat ) {
		for ( $i = 0; $i < count( $lonlat ); $i += 2 ) {
			/* lon */
			$lonlat[$i] = $lonlat[$i] * ( 2 * M_PI * 6378137 / 2.0 ) / 180.0;
			
			/* lat */
			$lonlat[$i + 1] = log( tan( ( 90 + $lonlat[$i + 1] ) * M_PI / 360.0 ) ) / ( M_PI / 180.0 );
			$lonlat[$i + 1] = $lonlat[$i + 1] * ( 2 * M_PI * 6378137 / 2.0 ) / 180.0;
		}
		return $lonlat;
	}
	
	/**
	 * Convert from spherical mercator to WGS84.
	 */
	private static function inverseMercator( array $lonlat ) {
		for ( $i = 0; $i < count( $lonlat ); $i += 2 ) {
			/* lon */
			$lonlat[$i] = $lonlat[$i] / ( ( 2 * M_PI * 6378137 / 2.0 ) / 180.0 );
			
			/* lat */
			$lonlat[$i + 1] = $lonlat[$i + 1] / ( ( 2 * M_PI * 6378137 / 2.0 ) / 180.0 );
			$lonlat[$i + 1] = 180.0 / M_PI * ( 2 * atan( exp( $lonlat[$i + 1] * M_PI / 180.0 ) ) - M_PI / 2 );
		}
		
		return $lonlat;
	}
    
}

