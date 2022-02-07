<?php

declare( strict_types = 1 );

namespace Maps\Map;

class MapData {

	private array $parameters;

	/**
	 * @var Marker[]
	 */
	private array $markers = [];

	public function __construct( array $parameters ) {
		$this->parameters = $parameters;
	}

	public function getParameters(): array {
		return $this->parameters;
	}

	public function setParameters( array $parameters ): void {
		$this->parameters = $parameters;
	}

	/**
	 * @return Marker[]
	 */
	public function getMarkers(): array {
		return $this->markers;
	}

	/**
	 * @param Marker[] $markers
	 */
	public function setMarkers( array $markers ): void {
		$this->markers = $markers;
	}

}
