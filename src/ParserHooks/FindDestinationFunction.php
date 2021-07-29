<?php

declare( strict_types = 1 );

namespace Maps\ParserHooks;

use DataValues\Geo\Values\LatLongValue;
use Maps\GeoFunctions;
use Maps\MapsFactory;
use ParamProcessor\ProcessingResult;
use Parser;
use ParserHooks\HookDefinition;
use ParserHooks\HookHandler;

/**
 * Class for the 'finddestination' parser hooks, which can find a
 * destination given a starting point, an initial bearing and a distance.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FindDestinationFunction implements HookHandler {

	public function handle( Parser $parser, ProcessingResult $result ) {
		foreach ( $result->getErrors() as $error ) {
			if ( $error->isFatal() ) {
				return '<div><span class="errorbox">' .
					wfMessage( 'validator-fatal-error', $error->getMessage() )->parse() .
					'</span></div><br /><br />';
			}
		}

		$parameters = $result->getParameters();

		$destination = GeoFunctions::findDestination(
			$parameters['location']->getValue()->getCoordinates(),
			$parameters['bearing']->getValue(),
			$parameters['distance']->getValue()
		);

		return MapsFactory::globalInstance()->getCoordinateFormatter()->format(
			new LatLongValue( $destination['lat'], $destination['lon'] ),
			$parameters['format']->getValue(),
			$parameters['directional']->getValue()
		);
	}

	public static function getHookDefinition(): HookDefinition {
		return new HookDefinition(
			'finddestination',
			self::getParameterInfo(),
			[ 'location', 'bearing', 'distance' ]
		);
	}

	private static function getParameterInfo(): array {
		global $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation, $egMapsCoordinateDirectional;

		$params = [];

		$params['location'] = [
			'type' => 'mapslocation',
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

		$params['bearing'] = [
			'type' => 'float',
		];

		$params['distance'] = [
			'type' => 'distance',
		];

		// Give grep a chance to find the usages:
		// maps-finddestination-par-location, maps-finddestination-par-format,
		// maps-finddestination-par-directional, maps-finddestination-par-bearing,
		// maps-finddestination-par-distance, maps-finddestination-par-mappingservice,
		// maps-finddestination-par-geoservice, maps-finddestination-par-allowcoordinates
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-finddestination-par-' . $name;
		}

		return $params;
	}

}
