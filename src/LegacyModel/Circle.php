<?php

declare( strict_types = 1 );

namespace Maps\LegacyModel;

use DataValues\Geo\Values\LatLongValue;
use InvalidArgumentException;

/**
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Circle extends \Maps\LegacyModel\BaseFillableElement {

	private LatLongValue $circleCentre;
	private float $circleRadius;

	public function __construct( LatLongValue $circleCentre, float $circleRadius ) {
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

	public function setCircleCentre( LatLongValue $circleCentre ): void {
		$this->circleCentre = $circleCentre;
	}

	public function getCircleRadius(): float {
		return $this->circleRadius;
	}

	public function setCircleRadius( float $circleRadius ): void {
		if ( $circleRadius <= 0 ) {
			throw new InvalidArgumentException( '$circleRadius must be greater than zero, got "' . $circleRadius . '"' );
		}

		$this->circleRadius = $circleRadius;
	}

}
