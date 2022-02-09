<?php

declare( strict_types = 1 );

namespace Maps;

use DataValues\Geo\Parsers\LatLongParser;
use FileFetcher\Cache\Factory as CacheFactory;
use FileFetcher\FileFetcher;
use Jeroen\SimpleGeocoder\Geocoder;
use Jeroen\SimpleGeocoder\Geocoders\Decorators\CoordinateFriendlyGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\FileFetchers\GeoNamesGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\FileFetchers\GoogleGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\FileFetchers\NominatimGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\NullGeocoder;
use Maps\DataAccess\CachingGeocoder;
use Maps\DataAccess\GeoJsonFetcher;
use Maps\DataAccess\ImageRepository;
use Maps\DataAccess\MapsFileFetcher;
use Maps\DataAccess\MediaWikiFileUrlFinder;
use Maps\DataAccess\MwImageRepository;
use Maps\DataAccess\PageContentFetcher;
use Maps\GeoJsonPages\GeoJsonStore;
use Maps\GeoJsonPages\Semantic\SemanticGeoJsonStore;
use Maps\GeoJsonPages\Semantic\SubObjectBuilder;
use Maps\Map\CargoFormat\CargoOutputBuilder;
use Maps\Map\DisplayMap\DisplayMapFunction;
use Maps\Map\MapOutputBuilder;
use Maps\Presentation\CoordinateFormatter;
use Maps\WikitextParsers\CircleParser;
use Maps\WikitextParsers\DistanceParser;
use Maps\WikitextParsers\ImageOverlayParser;
use Maps\WikitextParsers\LineParser;
use Maps\WikitextParsers\LocationParser;
use Maps\WikitextParsers\PolygonParser;
use Maps\WikitextParsers\RectangleParser;
use Maps\WikitextParsers\WmsOverlayParser;
use MediaWiki\MediaWikiServices;
use ParamProcessor\ParamDefinitionFactory;
use Parser;
use RepoGroup;
use SimpleCache\Cache\Cache;
use SimpleCache\Cache\MediaWikiCache;
use SMW\ApplicationFactory;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsFactory {

	protected static ?self $globalInstance = null;

	private array $settings;
	private MediaWikiServices $mediaWikiServices;

	private LeafletService $leafletService;
	private GoogleMapsService $googleService;

	final protected function __construct( array $settings, MediaWikiServices $mediaWikiServices ) {
		$this->settings = $settings;
		$this->mediaWikiServices = $mediaWikiServices;
	}

	/**
	 * Only for legacy code where dependency injection is not possible
	 */
	public static function globalInstance(): self {
		if ( self::$globalInstance === null ) {
			self::$globalInstance = self::newDefault();
		}

		return self::$globalInstance;
	}

	protected static function newDefault(): self {
		return new static( $GLOBALS, MediaWikiServices::getInstance() );
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

		return ( new CacheFactory() )->newJeroenSimpleCacheFetcher(
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
		return \ObjectCache::getInstance( $this->settings['egMapsGeoCacheType'] );
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

	public function getMappingServices(): MappingServices {
		return new MappingServices(
			$this->settings['egMapsAvailableServices'],
			$this->settings['egMapsDefaultService'],
			$this->getGoogleMapsService(),
			$this->getLeafletService()
		);
	}

	private function getGoogleMapsService(): GoogleMapsService {
		$this->googleService ??= new GoogleMapsService();

		return $this->googleService;
	}

	private function getLeafletService(): LeafletService {
		$this->leafletService ??= new LeafletService(
			$this->getImageRepository()
		);

		return $this->leafletService;
	}

	public function getDisplayMapFunction(): DisplayMapFunction {
		return new DisplayMapFunction(
			$this->getMappingServices()
		);
	}

	public function getParamDefinitionFactory(): ParamDefinitionFactory {
		$factory = ParamDefinitionFactory::newDefault();

		$factory->registerType( 'coordinate', [ 'string-parser' => LatLongParser::class ] );
		$factory->registerType( 'mapslocation', [ 'string-parser' => LocationParser::class ] );
		$factory->registerType( 'mapsline', [ 'string-parser' => LineParser::class ] );
		$factory->registerType( 'mapscircle', [ 'string-parser' => CircleParser::class ] );
		$factory->registerType( 'mapsrectangle', [ 'string-parser' => RectangleParser::class ] );
		$factory->registerType( 'mapspolygon', [ 'string-parser' => PolygonParser::class ] );
		$factory->registerType( 'distance', [ 'string-parser' => DistanceParser::class ] );
		$factory->registerType( 'wmsoverlay', [ 'string-parser' => WmsOverlayParser::class ] );
		$factory->registerType( 'mapsimageoverlay', [ 'string-parser' => ImageOverlayParser::class ] );

		return $factory;
	}

	public function newGeoJsonFetcher( FileFetcher $fileFetcher = null ): GeoJsonFetcher {
		return new GeoJsonFetcher(
			$fileFetcher ?? $this->getGeoJsonFileFetcher(),
			$this->mediaWikiServices->getTitleParser(),
			$this->mediaWikiServices->getRevisionLookup()
		);
	}

	public function smwIntegrationIsEnabled(): bool {
		return !$this->settings['egMapsDisableSmwIntegration'] && defined( 'SMW_VERSION' );
	}

	public function newSemanticMapsSetup(): SemanticMapsSetup {
		return new SemanticMapsSetup( $this->getMappingServices() );
	}

	public function newSemanticGeoJsonStore( \ParserOutput $parserOutput, Title $subjectPage ): GeoJsonStore {
		return new SemanticGeoJsonStore(
			$this->getSmwFactory()->newParserData( $subjectPage, $parserOutput ),
			$subjectPage,
			$this->getSmwFactory()->getEventDispatcher(),
			new SubObjectBuilder()
		);
	}

	private function getSmwFactory(): ApplicationFactory {
		return ApplicationFactory::getInstance();
	}

	public function getImageRepository(): ImageRepository {
		return new MwImageRepository(
			$this->getRepoGroup()
		);
	}

	private function getRepoGroup(): RepoGroup {
		return MediaWikiServices::getInstance()->getRepoGroup();
	}

	public function getMapOutputBuilder(): MapOutputBuilder {
		return new MapOutputBuilder(
//			$this->getMappingServices(),
//			$this->getParamDefinitionFactory()
		);
	}

	public function newCargoOutputBuilder(): CargoOutputBuilder {
		return new CargoOutputBuilder(
			$this->getMapOutputBuilder(),
			$this->getMappingServices(),
			$this->getParamDefinitionFactory(),
			$this->getImageRepository()
		);
	}

	public function newParserHookSetup( Parser $parser ): ParserHookSetup {
		return new ParserHookSetup(
			$parser,
			$this->settings['egMapsEnableCoordinateFunction']
		);
	}

}
