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
use PPFrame;

class ParserHookSetup {

	public function registerParserHooks( array &$mwGlobals ) {
		$this->registerDisplayMap( $mwGlobals );
		$this->registerNonMapHooks( $mwGlobals );
	}

	private function registerDisplayMap( array &$mwGlobals ) {
		$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			foreach ( [ 'display_map', 'display_point', 'display_points', 'display_line' ] as $hookName ) {
				$parser->setFunctionHook(
					$hookName,
					function ( Parser $parser, PPFrame $frame, array $arguments ) {
						return $this->handleFunctionHook( $parser, $frame, $arguments );
					},
					Parser::SFH_OBJECT_ARGS
				);

				$parser->setHook(
					$hookName,
					function ( $text, array $arguments, Parser $parser ) {
						if ( $text !== null ) {
							$arguments[DisplayMapFunction::getDefaultParameters()[0]] = $text;
						}

						return $this->getFactory()->getDisplayMapFunction()->getMapHtmlForParameterList( $parser, $arguments );
					}
				);
			}
		};
	}

	private function handleFunctionHook( Parser $parser, PPFrame $frame, array $arguments ): array {
		$mapHtml = $this->getFactory()->getDisplayMapFunction()->getMapHtmlForKeyValueStrings(
			$parser,
			array_map(
				function ( $argument ) use ( $frame ) {
					return $frame->expand( $argument );
				},
				$arguments
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

	private function registerNonMapHooks( array &$mwGlobals ) {
		if ( $mwGlobals['egMapsEnableCoordinateFunction'] ) {
			$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
				return ( new CoordinatesFunction() )->init( $parser );
			};
		}

		$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new DistanceFunction() )->init( $parser );
		};

		$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new FindDestinationFunction() )->init( $parser );
		};

		$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new GeocodeFunction() )->init( $parser );
		};

		$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new GeoDistanceFunction() )->init( $parser );
		};

		$mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new MapsDocFunction() )->init( $parser );
		};
	}

}
