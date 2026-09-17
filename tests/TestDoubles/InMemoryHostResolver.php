<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\DataAccess\HostResolver;

class InMemoryHostResolver implements HostResolver {

	/**
	 * @param array<string, string[]> $addressesByHost
	 */
	public function __construct(
		private array $addressesByHost
	) {
	}

	/**
	 * @return string[]
	 */
	public function resolve( string $host ): array {
		return $this->addressesByHost[$host] ?? [];
	}

}
