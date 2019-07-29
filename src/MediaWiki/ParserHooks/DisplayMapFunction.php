<?php

namespace Maps\MediaWiki\ParserHooks;

use Maps;
use Maps\MappingServices;
use Maps\MapsFactory;
use Maps\Presentation\ParameterExtractor;
use MWException;
use ParamProcessor\ParamDefinition;
use ParamProcessor\ParamDefinitionFactory;
use ParamProcessor\ProcessedParam;
use ParamProcessor\Processor;
use Parser;
use ParserHooks\HookDefinition;

/**
 * Class for the 'display_map' parser hooks.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DisplayMapFunction {

	private $services;

	private $renderer;

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
			self::getHookDefinition( ';' )->getDefaultParameters()
		);

		$processor->setParameterDefinitions(
			// TODO use ParamDefinitionFactory
			ParamDefinition::getCleanDefinitions(
				array_merge(
					self::getHookDefinition( ';' )->getParameters(),
					$service->getParameterInfo()
				)
			)
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
		$processor = new Processor( new \ParamProcessor\Options() );

		$service = $this->services->getServiceOrDefault( $this->extractServiceName( $parameters ) );

		$this->renderer->service = $service;

		$processor->setParameters( $parameters );
		$processor->setParameterDefinitions(
			// TODO use ParamDefinitionFactory
			ParamDefinition::getCleanDefinitions(
				array_merge(
					self::getHookDefinition( "\n" )->getParameters(),
					$service->getParameterInfo()
				)
			)
		);

		return $this->getMapHtmlFromProcessor( $parser, $processor );
	}

	private function getMapHtmlFromProcessor( Parser $parser, Processor $processor ) {
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

		return $service ?? '';
	}

	private function processedParametersToKeyValueArray( array $params ): array {
		$parameters = [];

		foreach ( $params as $parameter ) {
			$parameters[$parameter->getName()] = $parameter->getValue();
		}

		return $parameters;
	}

	public static function getHookDefinition( string $locationDelimiter ): HookDefinition {
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

		return new HookDefinition(
			[ 'display_map', 'display_point', 'display_points', 'display_line' ],
			$params,
			[ 'coordinates' ]
		);
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
