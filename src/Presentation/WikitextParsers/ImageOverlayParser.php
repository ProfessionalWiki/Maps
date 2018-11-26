<?php

namespace Maps\Presentation\WikitextParsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoder;
use Maps\Elements\ImageOverlay;
use Maps\MapsFactory;
use ValueParsers\ParseException;
use ValueParsers\ValueParser;

/**
 * @since 3.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImageOverlayParser implements ValueParser {

	private $geocoder;

	public function __construct( $geocoder = null ) {
		$this->geocoder = $geocoder instanceof Geocoder ? $geocoder : MapsFactory::newDefault()->getGeocoder();
	}

	/**
	 * @since 3.1
	 *
	 * @param string $value
	 *
	 * @return ImageOverlay
	 * @throws ParseException
	 */
	public function parse( $value ) {
		$metaData = explode( '~', $value );
		$imageParameters = explode( ':', array_shift( $metaData ), 3 );

		if ( count( $imageParameters ) !== 3 ) {
			throw new ParseException( 'Need 3 parameters for an image overlay' );
		}

		$boundsNorthEast = $this->stringToLatLongValue( $imageParameters[0] );
		$boundsSouthWest = $this->stringToLatLongValue( $imageParameters[1] );
		$imageUrl = \Maps\MapsFunctions::getFileUrl( $imageParameters[2] );

		$overlay = new ImageOverlay( $boundsNorthEast, $boundsSouthWest, $imageUrl );

		if ( $metaData !== [] ) {
			$overlay->setTitle( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$overlay->setText( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$overlay->setLink( $this->getUrlFromLinkString( array_shift( $metaData ) ) );
		}

		return $overlay;
	}

	private function getUrlFromLinkString( string $linkString ): string {
		$linkPrefix = 'link:';

		if ( substr( $linkString, 0, strlen( $linkPrefix ) ) === $linkPrefix ) {
			return substr( $linkString, strlen( $linkPrefix ) );
		}

		return $linkString;
	}

	private function stringToLatLongValue( string $location ): LatLongValue {
		$latLong = $this->geocoder->geocode( $location );

		if ( $latLong === null ) {
			throw new ParseException( 'Failed to parse or geocode' );
		}

		return $latLong;
	}

}
