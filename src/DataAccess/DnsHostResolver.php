<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * @licence GNU GPL v2+
 */
class DnsHostResolver implements HostResolver {

	/**
	 * @return string[]
	 */
	public function resolve( string $host ): array {
		$records = dns_get_record( $host, DNS_A | DNS_AAAA );

		if ( $records === false ) {
			return [];
		}

		return array_merge(
			array_column( $records, 'ip' ),
			array_column( $records, 'ipv6' )
		);
	}

}
