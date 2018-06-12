<?php

use ParamProcessor\ProcessedParam;
use ParamProcessor\ProcessingResult;

/**
 * Class for the 'display_map' parser hooks.
 *
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDisplayMap implements \ParserHooks\HookHandler {

	private $renderer;

	public function __construct() {
		$this->renderer = new MapsDisplayMapRenderer();
	}

	public function handle( Parser $parser, ProcessingResult $result ) {
		$params = $result->getParameters();

		$this->defaultMapZoom( $params );

		$parameters = [];

		foreach ( $params as $parameter ) {
			$parameters[$parameter->getName()] = $parameter->getValue();
		}

		$this->trackMap( $parser );

		// TODO: do not use global access
		$this->renderer->service = MapsMappingServices::getServiceInstance( $parameters['mappingservice'] );

		return $this->renderer->renderMap( $parameters, $parser );
	}

	public static function getHookDefinition( string $locationDelimiter ): \ParserHooks\HookDefinition {
		return new \ParserHooks\HookDefinition(
			[ 'display_map', 'display_point', 'display_points', 'display_line' ],
			self::getParameterDefinitions( $locationDelimiter ),
			[ 'coordinates' ]
		);
	}

	private static function getParameterDefinitions( $locationDelimiter ): array {
		$params = MapsMapper::getCommonParameters();

		$params['mappingservice']['feature'] = 'display_map';

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
