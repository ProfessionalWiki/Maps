<?php

namespace Maps;
use MapsLocation;
use DataValues\GeoCoordinateValue;

/**
 * Class representing a circle.
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
class Circle extends \MapsBaseFillableElement {

	/**
	 * @var GeoCoordinateValue
	 */
	protected $circleCentre;

	/**
	 * @var integer|float
	 */
	protected $circleRadius;

	/**
	 * @param GeoCoordinateValue $circleCentre
	 * @param integer|float $circleRadius
	 */
	public function __construct( GeoCoordinateValue $circleCentre , $circleRadius ) {
		parent::__construct();

		$this->setCircleCentre( $circleCentre );
		$this->setCircleRadius( $circleRadius );
	}

	/**
	 * @return GeoCoordinateValue
	 */
	public function getCircleCentre() {
		return $this->circleCentre;
	}

	/**
	 * @param GeoCoordinateValue $circleCentre
	 */
	public function setCircleCentre( GeoCoordinateValue $circleCentre ) {
		$this->circleCentre = $circleCentre;
	}

	/**
	 * @return integer|float
	 */
	public function getCircleRadius() {
		return $this->circleRadius;
	}

	/**
	 * @param integer|float $circleRadius
	 */
	public function setCircleRadius( $circleRadius ) {
		$this->circleRadius = $circleRadius;
	}

	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		$parentArray = parent::getJSONObject( $defText , $defTitle );

		$array = array(
			'centre' => array(
				'lon' => $this->getCircleCentre()->getLongitude(),
				'lat' => $this->getCircleCentre()->getLatitude()
			) ,
			'radius' => intval( $this->getCircleRadius() ),
		);

		return array_merge( $parentArray, $array );
	}

}
