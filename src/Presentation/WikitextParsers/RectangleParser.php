<?php

namespace Maps\Presentation\WikitextParsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoder;
use Maps\Elements\Rectangle;
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
class RectangleParser implements ValueParser {

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
	 * @return Rectangle
	 */
	public function parse( $value ) {
		$metaData = explode( $this->metaDataSeparator, $value );
		$rectangleData = explode( ':', array_shift( $metaData ) );

		$rectangle = new Rectangle(
			$this->stringToLatLongValue( $rectangleData[0] ),
			$this->stringToLatLongValue( $rectangleData[1] )
		);

		if ( $metaData !== [] ) {
			$rectangle->setTitle( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$rectangle->setText( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$rectangle->setStrokeColor( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$rectangle->setStrokeOpacity( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$rectangle->setStrokeWeight( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$rectangle->setFillColor( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$rectangle->setFillOpacity( array_shift( $metaData ) );
		}

		return $rectangle;
	}

	private function stringToLatLongValue( string $location ): LatLongValue {
		$latLong = $this->geocoder->geocode( $location );

		if ( $latLong === null ) {
			throw new ParseException( 'Failed to parse or geocode' );
		}

		return $latLong;
	}

}
