<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\LeafletConfigSource;

class StubLeafletConfigSource implements LeafletConfigSource {

	public function __construct(
		private ?array $leafletConfig
	) {
	}

	public function getLeafletConfig(): ?array {
		return $this->leafletConfig;
	}

}
