<?php

/**
 * GeoNames Web Services Documentation: http://www.geonames.org/export/geonames-search.html
 *
 * @file Maps_GeonamesGeocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * Thanks go to Joel Natividad for pointing me to the GeoNames services.
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsGeonamesGeocoder'] 	= __FILE__;
$egMapsGeoServices['geonames'] = 'MapsGeonamesGeocoder';

final class MapsGeonamesGeocoder extends MapsBaseGeocoder {
	
	/**
	 * @see MapsBaseGeocoder::geocode()
	 *
	 * @param string $address
	 * 
	 * NOTE: The service is now also available in JSON, so it might be nice to change to that.
	 */
	public static function geocode( $address ) {
		// Create the request url
		$requestURL = 'http://ws.geonames.org/search?q=' . urlencode( $address ) . '&maxRows=1&style=SHORT';
		 
		$result = Http::get( $requestURL );
	
		$lon = self::getXmlElementValue( $result, 'lng' );
		$lat = self::getXmlElementValue( $result, 'lat' );

		// In case one of the values is not found, return false
		if ( !$lon || !$lat ) return false;

		return array(
			'lat' => $lat,
			'lon' => $lon
		);
	}
	
}