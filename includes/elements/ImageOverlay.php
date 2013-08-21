<?php

namespace Maps;

use DataValues\LatLongValue;

/**
 * Class representing an image overlay.
 *
 * @since 3.0
 *
 * @ingroup Maps
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
	 */
	public function __construct( LatLongValue $boundsNorthEast, LatLongValue $boundsSouthWest, $image ) {
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
