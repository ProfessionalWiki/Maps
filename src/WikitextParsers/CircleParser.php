<?php

declare( strict_types = 1 );

namespace Maps\WikitextParsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoder;
use Maps\LegacyModel\Circle;
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

	private string $metaDataSeparator = '~';
	private Geocoder $geocoder;

	public function __construct( $geocoder = null ) {
		$this->geocoder = $geocoder instanceof Geocoder ? $geocoder : MapsFactory::globalInstance()->getGeocoder();
	}

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 */
	public function parse( $value ): Circle {
		$metaData = explode( $this->metaDataSeparator, $value );

		$circle = $this->buildCircle( array_shift( $metaData ) );

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

	private function buildCircle( string $circleWikitext ): Circle {
		$circleData = explode( ':', $circleWikitext );

		return new Circle(
			$this->stringToLatLongValue( $circleData[0] ),
			$this->extractRadius( $circleData )
		);
	}

	private function extractRadius( array $circleData ): float {
		if ( array_key_exists( 1, $circleData ) ) {
			$radius = (float)$circleData[1];

			if ( $radius > 0 ) {
				return $radius;
			}
		}

		return 1;
	}

	private function stringToLatLongValue( string $location ): LatLongValue {
		$latLong = $this->geocoder->geocode( $location );

		if ( $latLong === null ) {
			throw new ParseException( 'Failed to parse or geocode' );
		}

		return $latLong;
	}

}
