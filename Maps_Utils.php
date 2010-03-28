<?php

/**  
 * A class that holds static helper functions for common functionality that is map-spesific.
 * Non spesific functions are located in @see MapsParserFunctions
 *
 * @file Maps_Utils.php
 * @ingroup Maps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if ( ! defined ( 'MEDIAWIKI' ) ) {
	die ( 'Not an entry point.' );
}

// TODO: merge with parding done in the geo coord type located in SM.

class MapsUtils {

	/**
	 * Convert from WGS84 to spherical mercator.
	 */
	public static function forwardMercator( array $lonlat ) {
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
	public static function inverseMercator( array $lonlat ) {
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
