<?php

declare( strict_types = 1 );

namespace Maps\WikitextParsers;

use DataValues\Geo\Parsers\LatLongParser;
use Jeroen\SimpleGeocoder\Geocoder;
use Maps\FileUrlFinder;
use Maps\LegacyModel\Location;
use Maps\MapsFactory;
use Title;
use ValueParsers\ParseException;
use ValueParsers\StringValueParser;
use ValueParsers\ValueParser;

/**
 * ValueParser that parses the string representation of a location.
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocationParser implements ValueParser {

	private Geocoder $geocoder;
	private FileUrlFinder $fileUrlFinder;
	private bool $useAddressAsTitle;

	/**
	 * @deprecated Use newInstance instead
	 */
	public function __construct( $enableLegacyCrud = true ) {
		if ( $enableLegacyCrud ) {
			$this->geocoder = MapsFactory::globalInstance()->getGeocoder();
			$this->fileUrlFinder = MapsFactory::globalInstance()->getFileUrlFinder();
			$this->useAddressAsTitle = false;
		}
	}

	public static function newInstance( Geocoder $geocoder, FileUrlFinder $fileUrlFinder, bool $useAddressAsTitle = false ): self {
		$instance = new self( false );
		$instance->geocoder = $geocoder;
		$instance->fileUrlFinder = $fileUrlFinder;
		$instance->useAddressAsTitle = $useAddressAsTitle;
		return $instance;
	}

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @throws ParseException
	 */
	public function parse( $value ): Location {
		$separator = '~';

		$metaData = explode( $separator, $value );

		$coordinatesOrAddress = array_shift( $metaData );
		$coordinates = $this->geocoder->geocode( $coordinatesOrAddress );

		if ( $coordinates === null ) {
			throw new ParseException( 'Location is not a parsable coordinate and not a geocodable address' );
		}

		$location = new Location( $coordinates );

		if ( $metaData !== [] ) {
			$this->setTitleOrLink( $location, array_shift( $metaData ) );
		} else {
			if ( $this->useAddressAsTitle && $this->isAddress( $coordinatesOrAddress ) ) {
				$location->setTitle( $coordinatesOrAddress );
			}
		}

		if ( $metaData !== [] ) {
			$location->setText( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$location->setIcon( $this->fileUrlFinder->getUrlForFileName( array_shift( $metaData ) ) );
		}

		if ( $metaData !== [] ) {
			$location->setGroup( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$location->setInlineLabel( array_shift( $metaData ) );
		}

		if ( $metaData !== [] ) {
			$location->setVisitedIcon( $this->fileUrlFinder->getUrlForFileName( array_shift( $metaData ) ) );
		}

		return $location;
	}

	private function setTitleOrLink( Location $location, $titleOrLink ) {
		if ( $this->isLink( $titleOrLink ) ) {
			$this->setLink( $location, $titleOrLink );
		} else {
			$location->setTitle( $titleOrLink );
		}
	}

	private function isLink( $value ): bool {
		return strpos( $value, 'link:' ) === 0;
	}

	private function setLink( Location $location, string $link ) {
		$link = substr( $link, 5 );
		$location->setLink( $this->getExpandedLink( $link ) );
	}

	private function getExpandedLink( string $link ): string {
		if ( filter_var( $link, FILTER_VALIDATE_URL ) ) {
			return $link;
		}

		$title = Title::newFromText( $link );

		if ( $title === null ) {
			return '';
		}

		return $title->getFullURL();
	}

	private function isAddress( string $coordsOrAddress ): bool {
		$coordinateParser = new LatLongParser();

		try {
			$coordinateParser->parse( $coordsOrAddress );
		}
		catch ( ParseException $parseException ) {
			return true;
		}

		return false;
	}

}
