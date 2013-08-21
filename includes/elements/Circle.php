<?php

namespace Maps;

use DataValues\LatLongValue;
use Maps\Location;

/**
 * Class representing a circle.
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
	 * @var LatLongValue
	 */
	protected $circleCentre;

	/**
	 * @var integer|float
	 */
	protected $circleRadius;

	/**
	 * @param LatLongValue $circleCentre
	 * @param integer|float $circleRadius
	 */
	public function __construct( LatLongValue $circleCentre , $circleRadius ) {
		parent::__construct();

		$this->setCircleCentre( $circleCentre );
		$this->setCircleRadius( $circleRadius );
	}

	/**
	 * @return LatLongValue
	 */
	public function getCircleCentre() {
		return $this->circleCentre;
	}

	/**
	 * @param LatLongValue $circleCentre
	 */
	public function setCircleCentre( LatLongValue $circleCentre ) {
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
