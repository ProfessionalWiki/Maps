<?php

namespace Maps\Elements;

use MWException;

/**
 * @since 3.0
 *
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Polygon extends Line implements \iHoverableMapElement {

	/**
	 * @since 3.0
	 *
	 * @var boolean
	 */
	protected $onlyVisibleOnHover = false;

	/**
	 * @since 3.0
	 *
	 * @param boolean $visible
	 *
	 * @throws MWException
	 */
	public function setOnlyVisibleOnHover( $visible ) {
		if ( !is_bool( $visible ) ) {
			throw new MWException( '$visible should be a boolean' );
		}

		$this->onlyVisibleOnHover = $visible;
	}

	/**
	 * @since 3.0
	 *
	 * @return boolean
	 */
	public function isOnlyVisibleOnHover() {
		return $this->onlyVisibleOnHover;
	}

	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		$json = parent::getJSONObject( $defText, $defTitle );

		$json['onlyVisibleOnHover'] = $this->isOnlyVisibleOnHover();

		return $json;
	}

}
