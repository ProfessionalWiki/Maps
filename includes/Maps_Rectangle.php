<?php

namespace Maps;
use MapsLocation;
use DataValues\GeoCoordinateValue;

/**
 * Class that holds metadata on rectangles made up by locations on map.
 *
 * @since 3.0
 *
 * @file Maps_Rectangle.php
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Rectangle extends \MapsBaseFillableElement {

	/**
	 * @since 3.0
	 * @var GeoCoordinateValue
	 */
	protected $rectangleNorthEast;

	/**
	 * @since 3.0
	 * @var GeoCoordinateValue
	 */
	protected $rectangleSouthWest;

	/**
	 * @since 3.0
	 *
	 * @param GeoCoordinateValue $rectangleNorthEast
	 * @param GeoCoordinateValue $rectangleSouthWest
	 */
	public function __construct( GeoCoordinateValue $rectangleNorthEast, GeoCoordinateValue $rectangleSouthWest ) {
		$this->setRectangleNorthEast( $rectangleNorthEast );
		$this->setRectangleSouthWest( $rectangleSouthWest );
	}

	/**
	 * @since 3.0
	 *
	 * @return GeoCoordinateValue
	 */
	public function getRectangleNorthEast() {
		return $this->rectangleNorthEast;
	}

	/**
	 * @since 3.0
	 *
	 * @return GeoCoordinateValue
	 */
	public function getRectangleSouthWest() {
		return $this->rectangleSouthWest;
	}

	/**
	 * @since 3.0
	 *
	 * @param GeoCoordinateValue $rectangleSouthWest
	 */
	public function setRectangleSouthWest( GeoCoordinateValue $rectangleSouthWest ) {
		$this->rectangleSouthWest = $rectangleSouthWest;
	}

	/**
	 * @since 3.0
	 *
	 * @param GeoCoordinateValue $rectangleNorthEast
	 */
	public function setRectangleNorthEast( GeoCoordinateValue $rectangleNorthEast ) {
		$this->rectangleNorthEast = $rectangleNorthEast;
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
		$array = array(
			'ne' => array(
				'lon' => $this->getRectangleNorthEast()->getLongitude(),
				'lat' => $this->getRectangleNorthEast()->getLatitude()
			),
			'sw' => array(
				'lon' => $this->getRectangleSouthWest()->getLongitude(),
				'lat' => $this->getRectangleSouthWest()->getLatitude()
			),
		);

		return array_merge( $parentArray , $array );
	}

	/**
	 * Returns if the rectangle is valid.
	 *
	 * @since 3.0
	 *
	 * @return boolean
	 */
	public function isValid() {
		return true;
	}

}
