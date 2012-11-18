<?php

namespace Maps;

/**
 * Class that holds metadata on polygons made up by locations on map.
 *
 * @since 0.7.2
 *
 * @file Maps_Polygon.php
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 */
class Polygon extends Line implements \iHoverableMapElement {

	/**
	 * @var boolean
	 */
	protected $onlyVisibleOnHover = false;

	/**
	 * @param boolean $visible
	 */
	public function setOnlyVisibleOnHover( $visible ) {
		$this->onlyVisibleOnHover = $visible;
	}

	/**
	 * @return mixed
	 */
	public function isOnlyVisibleOnHover() {
		return $this->onlyVisibleOnHover;
	}

	// TODO: 'onlyVisibleOnHover' => $this->isOnlyVisibleOnHover()

}
