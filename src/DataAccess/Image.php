<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

interface Image {

	public function getUrl(): string;

	public function getWidthInPx(): int;

	public function getHeightInPx(): int;

}
