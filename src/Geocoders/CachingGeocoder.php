<?php

namespace Maps\Geocoders;

use BagOStuff;
use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoder;

/**
 * @since 5.0
 *
 * @licence GNU GPL v2+
 * @author HgO < hgo@batato.be >
 */
class CachingGeocoder implements Geocoder {

	private $geocoder;
	private $cache;

	public function __construct( Geocoder $geocoder, BagOStuff $cache ) {
		$this->geocoder = $geocoder;
		$this->cache = $cache;
	}

	/**
	 * @return LatLongValue|null
	 */
	public function geocode( string $address ) {
		$key = $this->cache->makeKey( __CLASS__, $address );

		$coordinates = $this->cache->get( $key );

		// There was no entry in the cache, so we retrieve the coordinates
		if ( $coordinates === false ) {
			$coordinates = $this->geocoder->geocode( $address );

			$this->cache->set( $key, $coordinates, BagOStuff::TTL_DAY );
		}

		return $coordinates;
	}
}
