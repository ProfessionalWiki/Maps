<?php

declare( strict_types = 1 );

namespace Maps;

use Maps\Map\DisplayMap\DisplayMapFunction;
use Parser;
use PPFrame;

class ParserHookSetup {

	private $parser;

	public function __construct( Parser $parser ) {
		$this->parser = $parser;
	}

	public function registerParserHooks() {
		 $this->registerServiceSpecificFunction( 'leaflet', 'leaflet' );
		 $this->registerServiceSpecificFunction( 'google_maps', 'googlemaps3' );

		$this->registerDisplayMap();
		$this->registerNonMapHooks();
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

	private function registerNonMapHooks() {
		foreach ( $this->getFactory()->newNonMapParserHooks() as $hook ) {
			$hook->init( $this->parser );
		}
	}

}
