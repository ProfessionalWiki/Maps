<?php

declare( strict_types = 1 );

namespace Maps\ParserHooks;

use Maps\MapsFactory;
use ParamProcessor\ProcessingResult;
use Parser;
use ParserHooks\HookDefinition;
use ParserHooks\HookHandler;

/**
 * Class for the 'coordinates' parser hooks,
 * which can transform the notation of a set of coordinates.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CoordinatesFunction implements HookHandler {

	public function handle( Parser $parser, ProcessingResult $result ) {
		foreach ( $result->getErrors() as $error ) {
			if ( $error->isFatal() ) {
				return '<div><span class="errorbox">' .
					wfMessage( 'validator-fatal-error', $error->getMessage() )->parse() .
				'</span></div><br /><br />';
			}
		}

		$parameters = $result->getParameters();

		return MapsFactory::globalInstance()->getCoordinateFormatter()->format(
			$parameters['location']->getValue(),
			$parameters['format']->getValue(),
			$parameters['directional']->getValue()
		);
	}

	public static function getHookDefinition(): HookDefinition {
		return new HookDefinition(
			'coordinates',
			self::getParameterInfo(),
			[ 'location', 'format', 'directional' ]
		);
	}

	private static function getParameterInfo(): array {
		global $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation;
		global $egMapsCoordinateDirectional;

		$params = [];

		$params['location'] = [
			'type' => 'coordinate',
		];

		$params['format'] = [
			'default' => $egMapsCoordinateNotation,
			'values' => $egMapsAvailableCoordNotations,
			'aliases' => 'notation',
			'tolower' => true,
		];

		$params['directional'] = [
			'type' => 'boolean',
			'default' => $egMapsCoordinateDirectional,
		];

		// Give grep a chance to find the usages:
		// maps-coordinates-par-location, maps-coordinates-par-format, maps-coordinates-par-directional
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-coordinates-par-' . $name;
		}

		return $params;
	}

}
