<?php

namespace Maps\Elements;

use DataValues\Geo\Values\LatLongValue;
use InvalidArgumentException;

/**
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Circle extends \Maps\Elements\BaseFillableElement {

	private $circleCentre;
	private $circleRadius;

	public function __construct( LatLongValue $circleCentre, float $circleRadius ) {
		if ( !is_float( $circleRadius ) && !is_int( $circleRadius ) ) {
			throw new InvalidArgumentException( '$circleRadius must be a float or int' );
		}

		if ( $circleRadius <= 0 ) {
			throw new InvalidArgumentException( '$circleRadius must be greater than zero' );
		}

		$this->setCircleCentre( $circleCentre );
		$this->setCircleRadius( $circleRadius );
	}

	public function getJSONObject( string $defText = '', string $defTitle = '' ): array {
		return array_merge(
			parent::getJSONObject( $defText, $defTitle ),
			[
				'centre' => [
					'lon' => $this->getCircleCentre()->getLongitude(),
					'lat' => $this->getCircleCentre()->getLatitude()
				],
				'radius' => intval( $this->getCircleRadius() ),
			]
		);
	}

	public function getCircleCentre(): LatLongValue {
		return $this->circleCentre;
	}

	public function setCircleCentre( LatLongValue $circleCentre ) {
		$this->circleCentre = $circleCentre;
	}

	public function getCircleRadius(): float {
		return $this->circleRadius;
	}

	public function setCircleRadius( float $circleRadius ) {
		$this->circleRadius = $circleRadius;
	}

}
