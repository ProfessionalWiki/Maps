<?php

namespace Maps;
use \Maps\Location;
use DataValues\GeoCoordinateValue;

/**
 * Class representing a rectangle.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 3.0
 *
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
		parent::__construct();

		// TODO: validate bounds are correct, if not, flip
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

}
