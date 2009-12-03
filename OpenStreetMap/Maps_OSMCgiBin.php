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

if( !defined( 'MEDIAWIKI' ) ) {
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
		$args =
			$this->options['base_url']
			. '?'
			. 'bbox=' . implode( ',', $this->bounds )
			. '&amp;scale=' . $this->scale
			. '&amp;format=' . $this->options['format'];

		// Hack to support my custom cgi-bin/export script
		if ( isset( $this->options['get_args'] ) ) {
			$args .=
				'&amp;maptype=' . $this->options['get_args']['maptype']
				. '&amp;locale=' . $this->lang;
		}

		return $args;
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
			// Calculate bounds within a spherical mercator projection if that is what the scale is based on
			$mercatorCenter = MapsUtils::forwardMercator( $center );
			$mbounds = array( 
				$mercatorCenter[0] - $w_deg / 2, 
				$mercatorCenter[1] - $h_deg / 2, 
				$mercatorCenter[0] + $w_deg / 2, 
				$mercatorCenter[1] + $h_deg / 2 
			);
			$this->bounds = MapsUtils::inverseMercator( $mbounds );
		}
		else {
			// Calculate bounds within WGS84
			$this->bounds = array( $center[0] - $w_deg / 2, $center[1] - $h_deg / 2, $center[0] + $w_deg / 2, $center[1] + $h_deg / 2 );
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
    
}

