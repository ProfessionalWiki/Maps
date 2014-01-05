<?php

namespace Maps\Elements;

use DataValues\LatLongValue;
use InvalidArgumentException;

/**
 * @since 3.0
 *
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImageOverlay extends Rectangle {

	/**
	 * @since 3.0
	 *
	 * @var string
	 */
	protected $image;

	/**
	 * Constructor.
	 *
	 * @since 3.0
	 *
	 * @param LatLongValue $boundsNorthEast
	 * @param LatLongValue $boundsSouthWest
	 * @param string $image
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( LatLongValue $boundsNorthEast, LatLongValue $boundsSouthWest, $image ) {
		if ( !is_string( $image ) ) {
			throw new InvalidArgumentException( '$image must be a string' );
		}

		parent::__construct( $boundsNorthEast, $boundsSouthWest );
		$this->image = $image;
	}

	/**
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

}
