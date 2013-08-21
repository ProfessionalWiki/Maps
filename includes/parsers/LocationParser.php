<?php

namespace Maps;

use DataValues\LatLongValue;
use MWException;
use ValueParsers\GeoCoordinateParser;
use ValueParsers\ParseException;
use ValueParsers\StringValueParser;

/**
 * ValueParser that parses the string representation of a location.
 *
 * @since 3.0
 *
 * @file
 * @ingroup ValueParsers
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocationParser extends StringValueParser {

	// TODO
	protected $supportGeocoding = true;

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Location
	 * @throws MWException
	 */
	public function stringParse( $value ) {
		$separator = '~';

		$metaData = explode( $separator, $value );

		$coordinates = $this->getCoordinates( array_shift( $metaData ) );

		$location = new Location( $coordinates );

		if ( $metaData !== array() ) {
			$location->setTitle( array_shift( $metaData ) );
		}

		if ( $metaData !== array() ) {
			$location->setText( array_shift( $metaData ) );
		}

		if ( $metaData !== array() ) {
			$location->setIcon( array_shift( $metaData ) );
		}

		return $location;
	}

	/**
	 * @since 3.0
	 *
	 * @param string $location
	 *
	 * @return LatLongValue
	 * @throws ParseException
	 */
	protected function getCoordinates( $location ) {
		if ( $this->supportGeocoding && \Maps\Geocoders::canGeocode() ) {
			$location = \Maps\Geocoders::attemptToGeocode( $location );

			if ( $location === false ) {
				throw new ParseException( 'Failed to parse or geocode' );
			}

			assert( $location instanceof LatLongValue );
			return $location;
		}

		$parser = new GeoCoordinateParser( new \ValueParsers\ParserOptions() );
		return $parser->parse( $location );
	}

}
