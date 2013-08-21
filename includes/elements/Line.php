<?php

namespace Maps;

use DataValues\LatLongValue;
use MWException;

/**
 * Class representing a collection of LatLongValue objects forming a line.
 *
 * @since 3.0
 *
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Line extends \MapsBaseStrokableElement {

	/**
	 * @since 3.0
	 *
	 * @var LatLongValue[]
	 */
	protected $coordinates;

	/**
	 * @since 3.0
	 *
	 * @param LatLongValue[] $coordinates
	 *
	 * @throws MWException
	 */
	public function __construct( array $coordinates = array() ) {
		foreach ( $coordinates as $coordinate ) {
			if ( !( $coordinate instanceof LatLongValue ) ) {
				throw new MWException( 'Can only construct Maps\Line with DataValues\LatLongValue objects' );
			}
		}

		$this->coordinates = $coordinates;

		parent::__construct();
	}

	/**
	 * @since 3.0
	 *
	 * @return LatLongValue[]
	 */
	public function getLineCoordinates() {
		return $this->coordinates;
	}

	/**
	 * @since 3.0
	 *
	 * @param string $defText
	 * @param string $defTitle
	 *
	 * @return array
	 */
	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		$parentArray = parent::getJSONObject( $defText , $defTitle );
		$posArray = array();

		foreach ( $this->coordinates as $mapLocation ) {
			$posArray[] = array(
				'lat' => $mapLocation->getLatitude() ,
				'lon' => $mapLocation->getLongitude()
			);
		}

		$posArray = array( 'pos' => $posArray );

		return array_merge( $parentArray , $posArray );
	}

}
