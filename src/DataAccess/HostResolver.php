<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * Resolves a hostname to the IP addresses it points to.
 *
 * @licence GNU GPL v2+
 */
interface HostResolver {

	/**
	 * @return string[] IPv4 and IPv6 addresses, empty when the host does not resolve
	 */
	public function resolve( string $host ): array;

}
