<?php

declare( strict_types = 1 );

namespace Maps;

use FileFetcher\Cache\Factory as CacheFactory;
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
class Services {

	private $services = [];

	public function register( MappingService $service ) {
		$this->services[$service->getName()] = $service;
	}

}
