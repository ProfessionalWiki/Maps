<?php

namespace Maps\MediaWiki\ParserHooks;

use Maps;
use Maps\MapsFunctions;
use Maps\MappingServices;
use Maps\Presentation\ParameterExtractor;
use MWException;
use ParamProcessor;
use ParamProcessor\ProcessedParam;
use Parser;

/**
 * Class for the 'display_map' parser hooks.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DisplayMapFunction {

	private $renderer;
	private $defaultService;
	private $availableServices;

	public function __construct() {
		$this->renderer = new DisplayMapRenderer();

		// TODO: inject
		$this->defaultService = $GLOBALS['egMapsDefaultService'];
		$this->availableServices = $GLOBALS['egMapsAvailableServices'];
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
		$processor = new \ParamProcessor\Processor( new \ParamProcessor\Options() );

		// TODO: do not use global access
		$service = MappingServices::getServiceInstance(
			$this->extractServiceName(
				Maps\Presentation\ParameterExtractor::extractFromKeyValueStrings( $parameters )
			)
		);

		$parameterDefinitions = self::getHookDefinition( ';' )->getParameters();
		$service->addParameterInfo( $parameterDefinitions );
		$this->renderer->service = $service;

		$processor->setFunctionParams(
			$parameters,
			$parameterDefinitions,
			self::getHookDefinition( ';' )->getDefaultParameters()
		);

		return $this->getMapHtmlFromProcessor( $parser, $processor );
	}

	/**
	 * @param Parser $parser
	 * @param string[] $parameters Key value list of parameters. Unnamed parameters have numeric keys.
	 * Both keys and values have not been normalized.
	 *
	 * @return string
	 * @throws MWException
	 */
	public function getMapHtmlForParameterList( Parser $parser, array $parameters ) {
		$processor = new \ParamProcessor\Processor( new \ParamProcessor\Options() );

		// TODO: do not use global access
		$service = MappingServices::getServiceInstance( $this->extractServiceName( $parameters ) );

		$parameterDefinitions = self::getHookDefinition( "\n" )->getParameters();
		$service->addParameterInfo( $parameterDefinitions );
		$this->renderer->service = $service;

		$processor->setParameters(
			$parameters,
			$parameterDefinitions
		);

		return $this->getMapHtmlFromProcessor( $parser, $processor );
	}

	private function getMapHtmlFromProcessor( Parser $parser, ParamProcessor\Processor $processor ) {
		$params = $processor->processParameters()->getParameters();

		$this->defaultMapZoom( $params );

		$this->trackMap( $parser );

		return $this->renderer->renderMap(
			$this->processedParametersToKeyValueArray( $params ),
			$parser
		);
	}

	private function extractServiceName( array $parameters ): string {
		$service = ( new ParameterExtractor() )->extract(
			[ 'mappingservice', 'service' ],
			$parameters
		);

		if ( $service === null ) {
			return $this->defaultService;
		}

		// TODO: do not use global access
		$service = MappingServices::getMainServiceName( $service );

		if ( $this->serviceIsInvalid( $service ) ) {
			return $this->defaultService;
		}

		return $service;
	}

	private function serviceIsInvalid( string $service ) {
		return !in_array( $service, $this->availableServices );
	}

	private function processedParametersToKeyValueArray( array $params ): array {
		$parameters = [];

		foreach ( $params as $parameter ) {
			$parameters[$parameter->getName()] = $parameter->getValue();
		}

		return $parameters;
	}

	public static function getHookDefinition( string $locationDelimiter ): \ParserHooks\HookDefinition {
		return new \ParserHooks\HookDefinition(
			[ 'display_map', 'display_point', 'display_points', 'display_line' ],
			self::getParameterDefinitions( $locationDelimiter ),
			[ 'coordinates' ]
		);
	}

	private static function getParameterDefinitions( $locationDelimiter ): array {
		$params = MapsFunctions::getCommonParameters();

		$params['coordinates'] = [
			'type' => 'string',
			'aliases' => [ 'coords', 'location', 'address', 'addresses', 'locations', 'points' ],
			'default' => [],
			'islist' => true,
			'delimiter' => $locationDelimiter,
			'message' => 'maps-displaymap-par-coordinates',
		];

		return $params;
	}

	/**
	 * @param ProcessedParam[] $parameters
	 */
	private function defaultMapZoom( array &$parameters ) {
		if ( array_key_exists( 'zoom', $parameters ) && $parameters['zoom']->wasSetToDefault() && count(
				$parameters['coordinates']->getValue()
			) > 1 ) {
			$parameters['zoom'] = $this->getParameterWithValue( $parameters['zoom'], false );
		}
	}

	private function getParameterWithValue( ProcessedParam $param, $value ) {
		return new ProcessedParam(
			$param->getName(),
			$value,
			$param->wasSetToDefault(),
			$param->getOriginalName(),
			$param->getOriginalValue()
		);
	}

	private function trackMap( Parser $parser ) {
		if ( $GLOBALS['egMapsEnableCategory'] ) {
			$parser->addTrackingCategory( 'maps-tracking-category' );
		}
	}

}
