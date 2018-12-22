<?php

declare( strict_types = 1 );

namespace Maps;

use FileFetcher\CachingFileFetcher;
use FileFetcher\FileFetcher;
use Jeroen\SimpleGeocoder\Geocoder;
use Jeroen\SimpleGeocoder\Geocoders\Decorators\CoordinateFriendlyGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\FileFetchers\GeoNamesGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\FileFetchers\GoogleGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\FileFetchers\NominatimGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\NullGeocoder;
use Maps\DataAccess\CachingGeocoder;
use Maps\DataAccess\MapsFileFetcher;
use Maps\DataAccess\MediaWikiFileUrlFinder;
use Maps\DataAccess\PageContentFetcher;
use Maps\Presentation\CoordinateFormatter;
use Maps\Presentation\WikitextParsers\LocationParser;
use MediaWiki\MediaWikiServices;
use SimpleCache\Cache\Cache;
use SimpleCache\Cache\MediaWikiCache;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsFactory {

	private $settings;
	private $mediaWikiServices;

	private function __construct( array $settings, MediaWikiServices $mediaWikiServices ) {
		$this->settings = $settings;
		$this->mediaWikiServices = $mediaWikiServices;
	}

	public static function newDefault(): self {
		return new self( $GLOBALS, MediaWikiServices::getInstance() );
	}

	/**
	 * Only for legacy code where dependency injection is not possible
	 */
	public static function globalInstance(): self {
		static $instance = null;

		if ( $instance === null ) {
			$instance = self::newDefault();
		}

		return $instance;
	}

	public function newLocationParser(): LocationParser {
		return LocationParser::newInstance(
			$this->getGeocoder(),
			$this->getFileUrlFinder()
		);
	}

	public function getGeocoder(): Geocoder {
		$geocoder = new CoordinateFriendlyGeocoder( $this->newCoreGeocoder() );

		if ( $this->settings['egMapsEnableGeoCache'] ) {
			return new CachingGeocoder(
				$geocoder,
				$this->getMediaWikiCache(),
				$this->settings['egMapsGeoCacheTtl']
			);
		}

		return $geocoder;
	}

	private function newCoreGeocoder(): Geocoder {
		switch ( $this->settings['egMapsDefaultGeoService'] ) {
			case 'geonames':
				if ( $this->settings['egMapsGeoNamesUser'] === '' ) {
					return $this->newGoogleGeocoder();
				}

				return new GeoNamesGeocoder(
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

	private function newGoogleGeocoder(): Geocoder {
		return new GoogleGeocoder(
			$this->newFileFetcher(),
			$this->settings['egMapsGMaps3ApiKey'],
			$this->settings['egMapsGMaps3ApiVersion']
		);
	}

	public function getFileFetcher(): FileFetcher {
		return $this->newFileFetcher();
	}

	private function newFileFetcher(): FileFetcher {
		return new MapsFileFetcher();
	}

	public function getGeoJsonFileFetcher(): FileFetcher {
		if ( $this->settings['egMapsGeoJsonCacheTtl'] === 0 ) {
			return $this->getFileFetcher();
		}

		return new CachingFileFetcher(
			$this->getFileFetcher(),
			$this->getMediaWikiSimpleCache( $this->settings['egMapsGeoJsonCacheTtl'] )
		);
	}

	private function getMediaWikiSimpleCache( int $ttlInSeconds ): Cache {
		return new MediaWikiCache(
			$this->getMediaWikiCache(),
			$ttlInSeconds
		);
	}

	private function getMediaWikiCache(): \BagOStuff {
		return wfGetCache( CACHE_ANYTHING );
	}

	public function getPageContentFetcher(): PageContentFetcher {
		return new PageContentFetcher(
			$this->mediaWikiServices->getTitleParser(),
			$this->mediaWikiServices->getRevisionLookup()
		);
	}

	public function getCoordinateFormatter(): CoordinateFormatter {
		return new CoordinateFormatter();
	}

	public function getFileUrlFinder(): FileUrlFinder {
		return new MediaWikiFileUrlFinder();
	}

}
