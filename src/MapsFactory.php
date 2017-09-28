<?php

namespace Maps;

use FileFetcher\FileFetcher;
use Maps\Geocoders\CachingGeocoder;
use Maps\Geocoders\CoordinateFriendlyGeocoder;
use Maps\Geocoders\Geocoder;
use Maps\Geocoders\GoogleGeocoder;
use Maps\Geocoders\NominatimGeocoder;
use Maps\Geocoders\NullGeocoder;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsFactory {

	private $settings;

	private function __construct( array $settings ) {
		$this->settings = $settings;
	}

	public static function newDefault() {
		return new self( $GLOBALS );
	}

	/**
	 * @return LocationParser
	 */
	public function newLocationParser() {
		return LocationParser::newInstance( $this->newGeocoder() );
	}

	/**
	 * @return Geocoder
	 */
	public function newGeocoder() {
		$geocoder = new CoordinateFriendlyGeocoder( $this->newCoreGeocoder() );

		if ( $this->settings['egMapsEnableGeoCache'] ) {
			return new CachingGeocoder(
				$geocoder,
				$this->getMediaWikiCache()
			);
		}

		return $geocoder;
	}

	/**
	 * @return Geocoder
	 */
	private function newCoreGeocoder() {
		switch ( $this->settings['egMapsDefaultGeoService'] ) {
			case 'geonames':
				if ( $this->settings['egMapsGeoNamesUser'] === '' ) {
					return $this->newGoogleGeocoder();
				}

				return new Geocoders\GeoNamesGeocoder(
					$this->newFileFetcher(),
					$this->settings['egMapsGeoNamesUser']
				);
			case 'google':
				return $this->newGoogleGeocoder();
			case 'nominatim':
				return new NominatimGeocoder(
					$this->newFileFetcher()
				);
			default:
				return new NullGeocoder();
		}
	}

	private function newGoogleGeocoder() {
		return new GoogleGeocoder(
			$this->newFileFetcher(),
			$this->settings['egMapsGMaps3ApiKey'],
			$this->settings['egMapsGMaps3ApiVersion']
		);
	}

	/**
	 * @return FileFetcher
	 */
	private function newFileFetcher() {
		return new MapsFileFetcher();
	}

	/**
	 * @return \BagOStuff
	 */
	private function getMediaWikiCache() {
		return wfGetCache( CACHE_ANYTHING );
	}

}
