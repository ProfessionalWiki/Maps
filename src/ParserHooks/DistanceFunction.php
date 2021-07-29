<?php

declare( strict_types = 1 );

namespace Maps\ParserHooks;

use Maps\Presentation\MapsDistanceParser;
use ParamProcessor\ProcessingResult;
use Parser;
use ParserHooks\HookDefinition;
use ParserHooks\HookHandler;

/**
 * Class for the 'distance' parser hooks,
 * which can transform the notation of a distance.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DistanceFunction implements HookHandler {

	public function handle( Parser $parser, ProcessingResult $result ) {
		foreach ( $result->getErrors() as $error ) {
			if ( $error->isFatal() ) {
				return '<div><span class="errorbox">' .
					wfMessage( 'validator-fatal-error', $error->getMessage() )->parse() .
					'</span></div><br /><br />';
			}
		}

		$parameters = $result->getParameters();

		return MapsDistanceParser::formatDistance(
			$parameters['distance']->getValue(),
			$parameters['unit']->getValue(),
			$parameters['decimals']->getValue()
		);
	}

	public static function getHookDefinition(): HookDefinition {
		return new HookDefinition(
			'distance',
			self::getParameterInfo(),
			[ 'distance', 'unit', 'decimals' ]
		);
	}

	private static function getParameterInfo(): array {
		global $egMapsDistanceUnit, $egMapsDistanceDecimals;

		$params = [];

		$params['distance'] = [
			'type' => 'distance',
		];

		$params['unit'] = [
			'default' => $egMapsDistanceUnit,
			'values' => MapsDistanceParser::getUnits(),
		];

		$params['decimals'] = [
			'type' => 'integer',
			'default' => $egMapsDistanceDecimals,
		];

		// Give grep a chance to find the usages:
		// maps-distance-par-distance, maps-distance-par-unit, maps-distance-par-decimals
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-distance-par-' . $name;
		}

		return $params;
	}

}
