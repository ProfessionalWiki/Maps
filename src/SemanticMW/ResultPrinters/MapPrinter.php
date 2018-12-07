<?php

namespace Maps\SemanticMW\ResultPrinters;

use FormatJson;
use Html;
use Linker;
use Maps\Elements\BaseElement;
use Maps\Elements\Location;
use Maps\FileUrlFinder;
use Maps\MappingService;
use Maps\MapsFunctions;
use Maps\MediaWiki\ParserHooks\DisplayMapRenderer;
use Maps\Presentation\ElementJsonSerializer;
use Maps\Presentation\WikitextParser;
use Maps\Presentation\WikitextParsers\LocationParser;
use ParamProcessor\ParamDefinition;
use Parser;
use SMW\Query\ResultPrinters\ResultPrinter;
use SMWOutputs;
use SMWQueryResult;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class MapPrinter extends ResultPrinter {

	private static $services = [];

	/**
	 * @var LocationParser
	 */
	private $locationParser;

	/**
	 * @var FileUrlFinder
	 */
	private $fileUrlFinder;

	/**
	 * @var MappingService
	 */
	private $service;

	/**
	 * @var WikitextParser
	 */
	private $wikitextParser;

	/**
	 * @var ElementJsonSerializer
	 */
	private $elementSerializer;

	/**
	 * @var string|boolean
	 */
	private $fatalErrorMsg = false;

	/**
	 * @param string $format
	 * @param bool $inline
	 */
	public function __construct( $format, $inline = true ) {
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

	/**
	 * Builds up and returns the HTML for the map, with the queried coordinate data on it.
	 *
	 * @param SMWQueryResult $res
	 * @param int $outputMode
	 *
	 * @return string
	 */
	public final function getResultText( SMWQueryResult $res, $outputMode ) {
		if ( $this->fatalErrorMsg !== false ) {
			return $this->fatalErrorMsg;
		}

		$factory = \Maps\MapsFactory::newDefault();
		$this->locationParser = $factory->newLocationParser();
		$this->fileUrlFinder = $factory->getFileUrlFinder();

		$this->wikitextParser = new WikitextParser( clone $GLOBALS['wgParser'] );
		$this->elementSerializer = new ElementJsonSerializer( $this->wikitextParser );

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

		$this->service->addHtmlDependencies(
			DisplayMapRenderer::getLayerDependencies( $params['format'], $params )
		);

		if ( $params['locations'] === [] ) {
			return $params['default'];
		}

		// We can only take care of the zoom defaulting here,
		// as not all locations are available in whats passed to Validator.
		if ( $this->fullParams['zoom']->wasSetToDefault() && count( $params['locations'] ) > 1 ) {
			$params['zoom'] = false;
		}

		$mapName = $this->service->getMapId();

		SMWOutputs::requireHeadItem(
			$mapName,
			$this->service->getDependencyHtml()
		);

		foreach ( $this->service->getResourceModules() as $resourceModule ) {
			SMWOutputs::requireResource( $resourceModule );
		}

		if ( array_key_exists( 'source', $params ) ) {
			unset( $params['source'] );
		}

		return $this->getMapHTML( $params, $mapName );
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
		/**
		 * @var Parser $wgParser
		 */
		global $wgParser;

		if ( $GLOBALS['egMapsEnableCategory'] && $wgParser->getOutput() !== null ) {
			$wgParser->addTrackingCategory( 'maps-tracking-category' );
		}
	}

	/**
	 * Converts the data in the coordinates parameter to JSON-ready objects.
	 * These get stored in the locations parameter, and the coordinates on gets deleted.
	 *
	 * @param array &$params
	 * @param QueryHandler $queryHandler
	 */
	private function handleMarkerData( array &$params, QueryHandler $queryHandler ) {
		$params['centre'] = $this->getCenter( $params['centre'] );

		$iconUrl = $this->fileUrlFinder->getUrlForFileName( $params['icon'] );
		$visitedIconUrl = $this->fileUrlFinder->getUrlForFileName( $params['visitedicon'] );

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
	 * Returns the HTML to display the map.
	 *
	 * @param array $params
	 * @param string $mapName
	 *
	 * @return string
	 */
	private function getMapHTML( array $params, string $mapName ): string {
		return Html::rawElement(
			'div',
			[
				'id' => $mapName,
				'style' => "width: {$params['width']}; height: {$params['height']}; background-color: #cccccc; overflow: hidden;",
				'class' => 'maps-map maps-' . $this->service->getName()
			],
			wfMessage( 'maps-loading-map' )->inContentLanguage()->escaped() .
			Html::element(
				'div',
				[ 'style' => 'display:none', 'class' => 'mapdata' ],
				FormatJson::encode( $params )
			)
		);
	}

	/**
	 * Returns the internationalized name of the mapping service.
	 *
	 * @return string
	 */
	public final function getName() {
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

		// Do not display this as an option, as the format already determines it
		// TODO: this can probably be done cleaner with some changes in Maps
		unset( $paramInfo['mappingservice'] );

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

		$params = ParamDefinition::getCleanDefinitions( MapsFunctions::getCommonParameters() );

		$this->service->addParameterInfo( $params );

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
