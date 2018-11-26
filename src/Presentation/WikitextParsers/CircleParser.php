<?php

namespace Maps\Presentation\WikitextParsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoder;
use Maps\Elements\Circle;
use Maps\MapsFactory;
use ValueParsers\ParseException;
use ValueParsers\StringValueParser;
use ValueParsers\ValueParser;

/**
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Kim Eik
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParser implements ValueParser {

	private $metaDataSeparator = '~';

	private $geocoder;

	public function __construct( $geocoder = null ) {
		$this->geocoder = $geocoder instanceof Geocoder ? $geocoder : MapsFactory::newDefault()->getGeocoder();
	}

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Circle
	 */
	public function parse( $value ) {
		$metaData = explode( $this->metaDataSeparator, $value );
		$circleData = explode( ':', array_shift( $metaData ) );

		$circle = new Circle( $this->stringToLatLongValue( $circleData[0] ), (float)$circleData[1] );

		if ( $metaData !== [] ) {
			$circle->setTitle( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$circle->setText( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$circle->setStrokeColor( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$circle->setStrokeOpacity( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$circle->setStrokeWeight( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$circle->setFillColor( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$circle->setFillOpacity( array_shift( $metaData ) );
		}

		return $circle;
	}

	private function stringToLatLongValue( string $location ): LatLongValue {
		$latLong = $this->geocoder->geocode( $location );

		if ( $latLong === null ) {
			throw new ParseException( 'Failed to parse or geocode' );
		}

		return $latLong;
	}

}
