<?php

namespace Maps\MediaWiki\ParserHooks;

use Jeroen\SimpleGeocoder\Geocoder;
use Maps\MapsFactory;
use ParserHook;

/**
 * Class for the 'geocode' parser hooks, which can turn
 * human readable locations into sets of coordinates.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeocodeFunction extends ParserHook {

	private $geocoder;

	public function __construct( Geocoder $geocoder = null ) {
		$this->geocoder = $geocoder ?? \Maps\MapsFactory::newDefault()->getGeocoder();
		parent::__construct();
	}

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
		$coordinates = $this->geocoder->geocode( $parameters['location'] );

		if ( $coordinates === null ) {
			return 'Geocoding failed'; // TODO: i18n
		}

		return MapsFactory::globalInstance()->getCoordinateFormatter()->format(
			$coordinates,
			$parameters['format'],
			$parameters['directional']
		);
	}

	/**
	 * @see ParserHook::getMessage()
	 */
	public function getMessage() {
		return 'maps-geocode-description';
	}

	/**
	 * Gets the name of the parser hook.
	 *
	 * @see ParserHook::getName
	 *
	 * @return string
	 */
	protected function getName() {
		return 'geocode';
	}

	/**
	 * Returns an array containing the parameter info.
	 *
	 * @see ParserHook::getParameterInfo
	 *
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
		global $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation;
		global $egMapsCoordinateDirectional;

		$params = [];

		$params['location'] = [
			'type' => 'string',
			'message' => 'maps-geocode-par-location',
		];

		$params['format'] = [
			'default' => $egMapsCoordinateNotation,
			'values' => $egMapsAvailableCoordNotations,
			'aliases' => 'notation',
			'tolower' => true,
			'message' => 'maps-geocode-par-format',
		];

		$params['directional'] = [
			'type' => 'boolean',
			'default' => $egMapsCoordinateDirectional,
			'message' => 'maps-geocode-par-directional',
		];

		return $params;
	}

	/**
	 * Returns the list of default parameters.
	 *
	 * @see ParserHook::getDefaultParameters
	 *
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return [ 'location' ];
	}

}
