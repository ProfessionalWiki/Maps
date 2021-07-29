<?php

declare( strict_types = 1 );

namespace Maps\ParserHooks;

use Maps\GeoFunctions;
use Maps\Presentation\MapsDistanceParser;
use ParamProcessor\ProcessingResult;
use Parser;
use ParserHooks\HookDefinition;
use ParserHooks\HookHandler;

/**
 * Class for the 'geodistance' parser hooks, which can
 * calculate the geographical distance between two points.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoDistanceFunction implements HookHandler {

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
			GeoFunctions::calculateDistance(
				$parameters['location1']->getValue()->getCoordinates(),
				$parameters['location2']->getValue()->getCoordinates()
			),
			$parameters['unit']->getValue(),
			$parameters['decimals']->getValue()
		);
	}

	public static function getHookDefinition(): HookDefinition {
		return new HookDefinition(
			'geodistance',
			self::getParameterInfo(),
			[ 'location1', 'location2', 'unit', 'decimals' ]
		);
	}

	private static function getParameterInfo(): array {
		global $egMapsDistanceUnit, $egMapsDistanceDecimals;

		$params = [];

		$params['unit'] = [
			'default' => $egMapsDistanceUnit,
			'values' => MapsDistanceParser::getUnits(),
		];

		$params['decimals'] = [
			'type' => 'integer',
			'default' => $egMapsDistanceDecimals,
		];

		$params['location1'] = [
			'type' => 'mapslocation',
			'aliases' => 'from',
		];

		$params['location2'] = [
			'type' => 'mapslocation',
			'aliases' => 'to',
		];

		// Give grep a chance to find the usages:
		// maps-geodistance-par-mappingservice, maps-geodistance-par-geoservice,
		// maps-geodistance-par-unit, maps-geodistance-par-decimals,
		// maps-geodistance-par-location1, maps-geodistance-par-location2
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-geodistance-par-' . $name;
		}

		return $params;
	}

}
