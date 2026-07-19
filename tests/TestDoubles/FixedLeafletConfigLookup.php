<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\LeafletConfig;
use Maps\LeafletConfigLookup;

class FixedLeafletConfigLookup implements LeafletConfigLookup {

	public function __construct(
		private LeafletConfig $config
	) {
	}

	public function getConfig(): LeafletConfig {
		return $this->config;
	}

}
