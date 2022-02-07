<?php

declare( strict_types = 1 );

namespace Maps\Map\DisplayMap;

use Maps;
use Maps\MappingService;
use Maps\MappingServices;
use Maps\MapsFactory;
use Maps\Presentation\ParameterExtractor;
use MWException;
use ParamProcessor\ParamDefinition;
use ParamProcessor\Processor;
use Parser;

/**
 * Class for the 'display_map' parser hooks.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DisplayMapFunction {

	private MappingServices $services;
	private DisplayMapRenderer $renderer;

	public function __construct( MappingServices $services ) {
		$this->services = $services;
		$this->renderer = new DisplayMapRenderer();
	}

	/**
	 * @param Parser $parser
	 * @param string[] $parameters Values of the array can be named parameters ("key=value") or unnamed.
	 * They are not normalized, so can be "key =  value "
	 *
	 * @return string
	 * @throws MWException
	 */
	public function getMapHtmlForKeyValueStrings( Parser $parser, array $parameters ): string {
		$processor = new Processor( new \ParamProcessor\Options() );

		$service = $this->services->getServiceOrDefault(
			$this->extractServiceName(
				Maps\Presentation\ParameterExtractor::extractFromKeyValueStrings( $parameters )
			)
		);

		$this->renderer->service = $service;

		$processor->setFunctionParams(
			$parameters,
			[],
			self::getDefaultParameters()
		);

		$processor->setParameterDefinitions(
			$this->getAllParameterDefinitions( $service, ';' )
		);

		$this->trackMap( $parser );

		$mapOutput = $this->renderer->renderMap(
			$service->newMapDataFromProcessingResult( $processor->processParameters() ),
			$parser
		);

		$mapOutput->addResourcesToParserOutput( $parser->getOutput() );

		return $mapOutput->getHtml();
	}

	/**
	 * @param Parser $parser
	 * @param string[] $parameters Key value list of parameters. Unnamed parameters have numeric keys.
	 * Both keys and values have not been normalized.
	 *
	 * @return string
	 * @throws MWException
	 */
	public function getMapHtmlForParameterList( Parser $parser, array $parameters ): string {
		$processor = new Processor( new \ParamProcessor\Options() );

		$service = $this->services->getServiceOrDefault( $this->extractServiceName( $parameters ) );

		$this->renderer->service = $service;

		$processor->setParameters( $parameters );
		$processor->setParameterDefinitions(
			$this->getAllParameterDefinitions( $service, "\n" )
		);

		$this->trackMap( $parser );

		$mapOutput = $this->renderer->renderMap(
			$service->newMapDataFromProcessingResult( $processor->processParameters() ),
			$parser
		);

		$mapOutput->addResourcesToParserOutput( $parser->getOutput() );

		return $mapOutput->getHtml();
	}

	/**
	 * @return ParamDefinition[]
	 */
	private function getAllParameterDefinitions( MappingService $service, string $locationDelimiter ): array {
		$params = [];

		$params['mappingservice'] = [
			'type' => 'string',
			'aliases' => 'service',
			'default' => $GLOBALS['egMapsDefaultService'],
			'values' => MapsFactory::globalInstance()->getMappingServices()->getAllNames(),
			'message' => 'maps-par-mappingservice'
		];

		$params['coordinates'] = [
			'type' => 'string',
			'aliases' => [ 'coords', 'location', 'address', 'addresses', 'locations', 'points' ],
			'default' => [],
			'islist' => true,
			'delimiter' => $locationDelimiter,
			'message' => 'maps-displaymap-par-coordinates',
		];

		return MapsFactory::globalInstance()->getParamDefinitionFactory()->newDefinitionsFromArrays(
			array_merge(
				$params,
				$service->getParameterInfo()
			)
		);
	}

	private function extractServiceName( array $parameters ): string {
		$service = ( new ParameterExtractor() )->extract(
			[ 'mappingservice', 'service' ],
			$parameters
		);

		return $service ?? '';
	}

	public static function getDefaultParameters(): array {
		return [ 'coordinates' ];
	}

	private function trackMap( Parser $parser ) {
		if ( $GLOBALS['egMapsEnableCategory'] ) {
			$parser->addTrackingCategory( 'maps-tracking-category' );
		}
	}

}
