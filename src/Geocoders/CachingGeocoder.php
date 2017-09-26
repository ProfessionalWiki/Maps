<?php

namespace Maps\Geocoders;

use DataValues\Geo\Values\LatLongValue;

/**
 * @since 5.0
 *
 * @licence GNU GPL v2+
 * @author HgO < hgo@batato.be >
 */
class CachingGeocoder implements Geocoder {
	protected $geocoder;
	
	protected $cache;

	/**
	 * @param Geocoder $geocoder
	 * @param BagOStuff $cache
	 *
	 */
	public function __construct( $geocoder, $cache ) {
		$this->geocoder = $geocoder;
		$this->cache = $cache;
	}
	
	/**
	 * @param string $address
	 *
	 * @return LatLongValue|null
	 */
	public function geocode( $address ) {
		$coordinates = $this->cache->get( $address );
		
		// There was no entry in the cache, so we retrieve the coordinates
		if ( $coordinates === false ) {
			$coordinates = $this->geocoder->geocode( $address );
			
			$this->cache->set( $address, $coordinates, $this->cache::TTL_DAY );
		}
		
		return $coordinates;
	}
}
