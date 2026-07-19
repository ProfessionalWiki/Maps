<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\Config\WikiConfigSource;

class StubWikiConfigSource implements WikiConfigSource {

	public function __construct(
		private ?array $config
	) {
	}

	public function getConfig(): ?array {
		return $this->config;
	}

}
