<?php

declare( strict_types = 1 );

namespace Maps\Map;

use DataValues\Geo\Values\LatLongValue;

class Marker {

	private LatLongValue $coordinates;
	private string $iconUrl = '';
	private string $text = '';

	public function __construct( LatLongValue $coordinates ) {
		$this->coordinates = $coordinates;
	}

	public function getCoordinates(): LatLongValue {
		return $this->coordinates;
	}

	public function getIconUrl(): string {
		return $this->iconUrl;
	}

	public function setIconUrl( string $iconUrl ): void {
		$this->iconUrl = $iconUrl;
	}

	public function getText(): string {
		return $this->text;
	}

	public function setText( string $text ): void {
		$this->text = $text;
	}

}
