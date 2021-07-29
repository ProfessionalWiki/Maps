<?php

declare( strict_types = 1 );

namespace Maps;

use Maps\Map\DisplayMap\DisplayMapFunction;
use Maps\ParserHooks\CoordinatesFunction;
use Maps\ParserHooks\DistanceFunction;
use Maps\ParserHooks\FindDestinationFunction;
use Maps\ParserHooks\GeocodeFunction;
use Maps\ParserHooks\GeoDistanceFunction;
use Maps\ParserHooks\MapsDocFunction;
use Parser;
use ParserHooks\HookRegistrant;
use PPFrame;

class ParserHookSetup {

	private Parser $parser;
	private bool $enableCoordinatesFunction;

	public function __construct( Parser $parser, bool $enableCoordinatesFunction ) {
		$this->parser = $parser;
		$this->enableCoordinatesFunction = $enableCoordinatesFunction;
	}

	public function registerParserHooks() {
		 $this->registerServiceSpecificFunction( 'leaflet', 'leaflet' );
		 $this->registerServiceSpecificFunction( 'google_maps', 'googlemaps3' );

		$hookRegistrant = new HookRegistrant( $this->parser );

		$this->registerDisplayMap();
		$this->registerCoordinates( $hookRegistrant );
		$this->registerDistance( $hookRegistrant );
		$this->registerFindDestination( $hookRegistrant );
		$this->registerGeocode( $hookRegistrant );
		$this->registerGeoDistance( $hookRegistrant );
		$this->registerMapsDoc( $hookRegistrant );
	}

	private function registerServiceSpecificFunction( string $functionName, string $serviceName ) {
		$this->parser->setFunctionHook(
			$functionName,
			function ( Parser $parser, PPFrame $frame, array $arguments ) use ( $serviceName ) {
				return $this->handleFunctionHook( $parser, $frame, $arguments, [ 'service=' . $serviceName ] );
			},
			Parser::SFH_OBJECT_ARGS
		);
	}

	private function registerDisplayMap() {
		foreach ( [ 'display_map', 'display_point', 'display_points', 'display_line' ] as $hookName ) {
			$this->parser->setFunctionHook(
				$hookName,
				function ( Parser $parser, PPFrame $frame, array $arguments ) {
					return $this->handleFunctionHook( $parser, $frame, $arguments );
				},
				Parser::SFH_OBJECT_ARGS
			);

			$this->parser->setHook(
				$hookName,
				function ( $text, array $arguments, Parser $parser ) {
					if ( $text !== null ) {
						$arguments[DisplayMapFunction::getDefaultParameters()[0]] = $text;
					}

					return $this->getFactory()->getDisplayMapFunction()->getMapHtmlForParameterList( $parser, $arguments );
				}
			);
		}
	}

	private function handleFunctionHook( Parser $parser, PPFrame $frame, array $arguments, $fixedArguments = [] ): array {
		$mapHtml = $this->getFactory()->getDisplayMapFunction()->getMapHtmlForKeyValueStrings(
			$parser,
			array_merge(
				array_map(
					function ( $argument ) use ( $frame ) {
						return $frame->expand( $argument );
					},
					$arguments
				),
				$fixedArguments
			)
		);

		return [
			$mapHtml,
			'noparse' => true,
			'isHTML' => true,
		];
	}

	private function getFactory(): MapsFactory {
		// Not injected into this class since that execution would happen before TestFactory setup
		return MapsFactory::globalInstance();
	}

	private function registerCoordinates( HookRegistrant $hookRegistrant ): void {
		if ( $this->enableCoordinatesFunction ) {
			$functionDefinition = CoordinatesFunction::getHookDefinition();
			$functionHandler = new CoordinatesFunction();
			$hookRegistrant->registerFunctionHandler( $functionDefinition, $functionHandler );
			$hookRegistrant->registerHookHandler( $functionDefinition, $functionHandler );
		}
	}

	private function registerDistance( HookRegistrant $hookRegistrant ): void {
		$functionDefinition = DistanceFunction::getHookDefinition();
		$functionHandler = new DistanceFunction();
		$hookRegistrant->registerFunctionHandler( $functionDefinition, $functionHandler );
		$hookRegistrant->registerHookHandler( $functionDefinition, $functionHandler );
	}

	private function registerFindDestination( HookRegistrant $hookRegistrant ): void {
		$functionDefinition = FindDestinationFunction::getHookDefinition();
		$functionHandler = new FindDestinationFunction();
		$hookRegistrant->registerFunctionHandler( $functionDefinition, $functionHandler );
		$hookRegistrant->registerHookHandler( $functionDefinition, $functionHandler );
	}

	private function registerGeocode( HookRegistrant $hookRegistrant ): void {
		$functionDefinition = GeocodeFunction::getHookDefinition();
		$functionHandler = new GeocodeFunction( MapsFactory::globalInstance()->getGeocoder() );
		$hookRegistrant->registerFunctionHandler( $functionDefinition, $functionHandler );
		$hookRegistrant->registerHookHandler( $functionDefinition, $functionHandler );
	}

	private function registerGeoDistance( HookRegistrant $hookRegistrant ): void {
		$functionDefinition = GeoDistanceFunction::getHookDefinition();
		$functionHandler = new GeoDistanceFunction();
		$hookRegistrant->registerFunctionHandler( $functionDefinition, $functionHandler );
		$hookRegistrant->registerHookHandler( $functionDefinition, $functionHandler );
	}

	private function registerMapsDoc( HookRegistrant $hookRegistrant ): void {
		$functionDefinition = MapsDocFunction::getHookDefinition();
		$functionHandler = new MapsDocFunction();
		$hookRegistrant->registerFunctionHandler( $functionDefinition, $functionHandler );
		$hookRegistrant->registerHookHandler( $functionDefinition, $functionHandler );
	}

}
