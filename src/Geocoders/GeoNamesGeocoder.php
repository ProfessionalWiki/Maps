<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;
use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;

/**
 * @since 4.5
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoNamesGeocoder implements Geocoder {

	private $fileFetcher;
	private $geoNamesUser;

	public function __construct( FileFetcher $fileFetcher, $geoNamesUser ) {
		$this->fileFetcher = $fileFetcher;
		$this->geoNamesUser = $geoNamesUser;
	}

	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		try {
			$response = $this->fileFetcher->fetchFile( $this->getRequestUrl( $address ) );
		}
		catch ( FileFetchingException $ex ) {
			return null;
		}

		$lon = self::getXmlElementValue( $response, 'lng' );
		$lat = self::getXmlElementValue( $response, 'lat' );

		if ( !$lon || !$lat ) {
			return null;
		}

		return new LatLongValue( (float)$lat, (float)$lon );
	}

	/**
	 * @param string $address
	 *
	 * @return string
	 */
	private function getRequestUrl( $address ) {
		return 'http://api.geonames.org/search?q='
			. urlencode( $address )
			. '&maxRows=1&username='
			. urlencode( $this->geoNamesUser );
	}

	private function getXmlElementValue( $xml, $tagName ) {
		$match = [];
		preg_match( "/<$tagName>(.*?)<\/$tagName>/", $xml, $match );
		return count( $match ) > 1 ? $match[1] : false;
	}

}
