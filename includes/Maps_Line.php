<?php

use DataValues\GeoCoordinateValue;

/**
 * Class that holds metadata on lines made up by locations on map.
 *
 * @since 2.0
 *
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 */
class MapsLine extends MapsBaseStrokableElement {

	/**
	 * @since 2.0
	 *
	 * @var GeoCoordinateValue[]
	 */
	protected $coordinates;

	/**
	 * @since 2.0
	 *
	 * @param GeoCoordinateValue[] $coordinates
	 */
	public function __construct( array $coordinates = array() ) {
		$this->coordinates = $coordinates;
	}

	/**
	 * @since 3.0
	 *
	 * @return GeoCoordinateValue[]
	 */
	public function getLineCoordinates() {
		return $this->coordinates;
	}

	/**
	 * @since 2.0
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
