<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\DataAccess\Image;

class ImageValueObject implements Image {

	private $url;
	private $widthInPx;
	private $heightInPx;

	public function __construct( string $url, int $widthInPx, int $heightInPx ) {
		$this->url = $url;
		$this->widthInPx = $widthInPx;
		$this->heightInPx = $heightInPx;
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getWidthInPx(): int {
		return $this->widthInPx;
	}

	public function getHeightInPx(): int {
		return $this->heightInPx;
	}

}
