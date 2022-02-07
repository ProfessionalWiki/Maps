<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use File;

class MwImage implements Image {

	private File $file;

	public function __construct( File $file ) {
		$this->file = $file;
	}

	public function getUrl(): string {
		return $this->file->getUrl();
	}

	public function getWidthInPx(): int {
		$width = $this->file->getWidth();
		return $width === false ? 0 : $width;
	}

	public function getHeightInPx(): int {
		$height = $this->file->getHeight();
		return $height === false ? 0 : $height;
	}

}
