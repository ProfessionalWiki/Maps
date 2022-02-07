<?php

declare( strict_types = 1 );

namespace Maps\Map\SemanticFormat;

use Linker;
use Maps\FileUrlFinder;
use Maps\LegacyModel\BaseElement;
use Maps\LegacyModel\Location;
use Maps\Map\MapOutput;
use Maps\Map\MapOutputBuilder;
use Maps\MappingService;
use Maps\Presentation\ElementJsonSerializer;
use Maps\Presentation\WikitextParser;
use Maps\SemanticMW\QueryHandler;
use Maps\WikitextParsers\LocationParser;
use MediaWiki\MediaWikiServices;
use Parser;
use SMW\Query\QueryResult;
use SMW\Query\ResultPrinters\ResultPrinter;
use SMWOutputs;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class MapPrinter extends ResultPrinter {

	/**
	 * @var array<int, MappingService>
	 */
	private static array $services = [];

	private LocationParser $locationParser;
	private FileUrlFinder $fileUrlFinder;
	private MappingService $service;
	private ElementJsonSerializer $elementSerializer;

	/**
	 * @var string|boolean
	 */
	private $fatalErrorMsg = false;

	public function __construct( string $format, bool $inline = true ) {
		$this->service = self::$services[$format];

		parent::__construct( $format, $inline );
	}

	/**
	 * @since 3.4
	 * FIXME: this is a temporary hack that should be replaced when SMW allows for dependency
	 * injection in query printers.
	 *
	 * @param MappingService $service
	 */
	public static function registerService( MappingService $service ) {
		self::$services[$service->getName()] = $service;
	}

	public static function registerDefaultService( $serviceName ) {
		self::$services['map'] = self::$services[$serviceName];
	}

	private function getParser(): Parser {
		return MediaWikiServices::getInstance()->getParser();
	}

	private function getParserClone(): Parser {
		$parser = $this->getParser();
		return clone $parser;
	}

	/**
	 * Builds up and returns the HTML for the map, with the queried coordinate data on it.
	 *
	 * @param QueryResult $res
	 * @param int $outputMode
	 *
	 * @return string
	 */
	final public function getResultText( QueryResult $res, $outputMode ): string {
		if ( $this->fatalErrorMsg !== false ) {
			return $this->fatalErrorMsg;
		}

		$this->isHTML = true;

		$factory = \Maps\MapsFactory::globalInstance();
		$this->locationParser = $factory->newLocationParser();
		$this->fileUrlFinder = $factory->getFileUrlFinder();

		$this->elementSerializer = new ElementJsonSerializer( new WikitextParser( $this->getParserClone() ) );

		$this->addTrackingCategoryIfNeeded();

		$params = $this->params;

		$queryHandler = new QueryHandler( $res, $outputMode );
		$queryHandler->setLinkStyle( $params['link'] );
		$queryHandler->setHeaderStyle( $params['headers'] );
		$queryHandler->setShowSubject( $params['showtitle'] );
		$queryHandler->setTemplate( $params['template'] );
		$queryHandler->setUserParam( $params['userparam'] );
		$queryHandler->setHideNamespace( $params['hidenamespace'] );
		$queryHandler->setActiveIcon( $params['activeicon'] );

		$this->handleMarkerData( $params, $queryHandler );

		$params['lines'] = $this->elementsToJson( $params['lines'] );
		$params['polygons'] = $this->elementsToJson( $params['polygons'] );
		$params['circles'] = $this->elementsToJson( $params['circles'] );
		$params['rectangles'] = $this->elementsToJson( $params['rectangles'] );

		$params['ajaxquery'] = urlencode( $params['ajaxquery'] );

		if ( $params['locations'] === [] ) {
			return $params['default'];
		}

		// We can only take care of the zoom defaulting here,
		// as not all locations are available in whats passed to Validator.
		if ( $this->fullParams['zoom']->wasSetToDefault() && count( $params['locations'] ) > 1 ) {
			$params['zoom'] = false;
		}

		if ( array_key_exists( 'source', $params ) ) {
			unset( $params['source'] );
		}

		$outputBuilder = new MapOutputBuilder();
		$mapOutput = $outputBuilder->buildOutput( $this->service, $this->service->newMapDataFromParameters( $params ) );

		$this->outputResources( $mapOutput );

		return $mapOutput->getHtml();
	}

	private function outputResources( MapOutput $mapOutput ) {
		SMWOutputs::requireHeadItem(
			$this->randomString(),
			$mapOutput->getHeadItems()
		);

		foreach ( $mapOutput->getResourceModules() as $resourceModule ) {
			SMWOutputs::requireResource( $resourceModule );
		}
	}

	private function randomString(): string {
		return substr( str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyz' ), 0, 10 );
	}

	private function elementsToJson( array $elements ) {
		return array_map(
			function( BaseElement $element ) {
				return $this->elementSerializer->elementToJson( $element );
			},
			$elements
		);
	}

	private function addTrackingCategoryIfNeeded() {
		$parser = MediaWikiServices::getInstance()->getParser();

		if ( $GLOBALS['egMapsEnableCategory'] && $parser->getOutput() !== null ) {
			$parser->addTrackingCategory( 'maps-tracking-category' );
		}
	}

	/**
	 * Converts the data in the coordinates parameter to JSON-ready objects.
	 * These get stored in the locations parameter, and the coordinates on gets deleted.
	 *
	 * @param array &$params
	 * @param \Maps\SemanticMW\QueryHandler $queryHandler
	 */
	private function handleMarkerData( array &$params, QueryHandler $queryHandler ) {
		$params['centre'] = $this->getCenter( $params['centre'] );

		$iconUrl = $this->fileUrlFinder->getUrlForFileName( $params['icon'] );
		$visitedIconUrl = $this->fileUrlFinder->getUrlForFileName( $params['visitedicon'] ?? '' );

		$params['locations'] = $this->getJsonForStaticLocations(
			$params['staticlocations'],
			$params,
			$iconUrl,
			$visitedIconUrl
		);

		unset( $params['staticlocations'] );

		$params['locations'] = array_merge(
			$params['locations'],
			$this->getJsonForLocations(
				$queryHandler->getLocations(),
				$params,
				$iconUrl,
				$visitedIconUrl
			)
		);
	}

	private function getCenter( $coordinatesOrAddress ) {
		if ( $coordinatesOrAddress === false ) {
			return false;
		}

		try {
			// FIXME: a Location makes no sense here, since the non-coordinate data is not used
			$location = $this->locationParser->parse( $coordinatesOrAddress );
		}
		catch ( \Exception $ex ) {
			// TODO: somehow report this to the user
			return false;
		}

		return $location->getJSONObject();
	}

	private function getJsonForStaticLocations( array $staticLocations, array $params, $iconUrl, $visitedIconUrl ) {
		$locationsJson = [];

		foreach ( $staticLocations as $location ) {
			$locationsJson[] = $this->getJsonForStaticLocation(
				$location,
				$params,
				$iconUrl,
				$visitedIconUrl
			);
		}

		return $locationsJson;
	}

	private function getJsonForStaticLocation( Location $location, array $params, $iconUrl, $visitedIconUrl ) {
		$jsonObj = $location->getJSONObject( $params['title'], $params['label'], $iconUrl, '', '', $visitedIconUrl );

		$this->elementSerializer->titleAndText( $jsonObj );

		if ( $params['pagelabel'] ) {
			$jsonObj['inlineLabel'] = Linker::link( Title::newFromText( $jsonObj['title'] ) );
		}

		return $jsonObj;
	}

	/**
	 * @param Location[] $locations
	 * @param array $params
	 * @param string $iconUrl
	 * @param string $visitedIconUrl
	 *
	 * @return array
	 */
	private function getJsonForLocations( iterable $locations, array $params, string $iconUrl, string $visitedIconUrl ): array {
		$locationsJson = [];

		foreach ( $locations as $location ) {
			$jsonObj = $location->getJSONObject(
				$params['title'],
				$params['label'],
				$iconUrl,
				'',
				'',
				$visitedIconUrl
			);

			$jsonObj['title'] = strip_tags( $jsonObj['title'] );

			$locationsJson[] = $jsonObj;
		}

		return $locationsJson;
	}

	/**
	 * Returns the internationalized name of the mapping service.
	 *
	 * @return string
	 */
	final public function getName() {
		return wfMessage( 'maps_' . $this->service->getName() )->text();
	}

	/**
	 * Returns a list of parameter information, for usage by Special:Ask and others.
	 *
	 * @return array
	 */
	public function getParameters() {
		$params = parent::getParameters();
		$paramInfo = $this->getParameterInfo();

		$params = array_merge( $params, $paramInfo );

		return $params;
	}

	/**
	 * Returns an array containing the parameter info.
	 *
	 * @return array
	 */
	private function getParameterInfo() {
		global $smgQPShowTitle, $smgQPTemplate, $smgQPHideNamespace;

		$params = $this->service->getParameterInfo();

		$params['staticlocations'] = [
			'type' => 'mapslocation',
			'aliases' => [ 'locations', 'points' ],
			'default' => [],
			'islist' => true,
			'delimiter' => ';',
			'message' => 'semanticmaps-par-staticlocations',
		];

		$params['showtitle'] = [
			'type' => 'boolean',
			'aliases' => 'show title',
			'default' => $smgQPShowTitle,
		];

		$params['hidenamespace'] = [
			'type' => 'boolean',
			'aliases' => 'hide namespace',
			'default' => $smgQPHideNamespace,
		];

		$params['template'] = [
			'default' => $smgQPTemplate,
		];

		$params['userparam'] = [
			'default' => '',
		];

		$params['activeicon'] = [
			'type' => 'string',
			'default' => '',
		];

		$params['pagelabel'] = [
			'type' => 'boolean',
			'default' => false,
		];

		$params['ajaxcoordproperty'] = [
			'default' => '',
		];

		$params['ajaxquery'] = [
			'default' => '',
			'type' => 'string'
		];

		// Messages:
		// semanticmaps-par-staticlocations, semanticmaps-par-showtitle, semanticmaps-par-hidenamespace,
		// semanticmaps-par-template, semanticmaps-par-userparam, semanticmaps-par-activeicon,
		// semanticmaps-par-pagelabel, semanticmaps-par-ajaxcoordproperty semanticmaps-par-ajaxquery
		foreach ( $params as $name => &$data ) {
			if ( is_array( $data ) && !array_key_exists( 'message', $data ) ) {
				$data['message'] = 'semanticmaps-par-' . $name;
			}
		}

		return $params;
	}
}
