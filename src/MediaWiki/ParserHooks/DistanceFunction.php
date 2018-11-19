<?php

namespace Maps\MediaWiki\ParserHooks;

use Maps\Presentation\MapsDistanceParser;
use ParserHook;

/**
 * Class for the 'distance' parser hooks,
 * which can transform the notation of a distance.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DistanceFunction extends ParserHook {

	/**
	 * Renders and returns the output.
	 *
	 * @see ParserHook::render
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	public function render( array $parameters ) {
		return MapsDistanceParser::formatDistance(
			$parameters['distance'],
			$parameters['unit'],
			$parameters['decimals']
		);
	}

	/**
	 * @see ParserHook::getMessage()
	 */
	public function getMessage() {
		return 'maps-distance-description';
	}

	/**
	 * Gets the name of the parser hook.
	 *
	 * @see ParserHook::getName
	 *
	 * @return string
	 */
	protected function getName() {
		return 'distance';
	}

	/**
	 * Returns an array containing the parameter info.
	 *
	 * @see ParserHook::getParameterInfo
	 *
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
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

	/**
	 * Returns the list of default parameters.
	 *
	 * @see ParserHook::getDefaultParameters
	 *
	 * @param $type
	 *
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return [ 'distance', 'unit', 'decimals' ];
	}

}