<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use Wikimedia\IPUtils;

/**
 * Checks whether a URL resolves to a private or reserved IP address.
 * Used to prevent Server-Side Request Forgery (SSRF) attacks.
 *
 * Note: This provides best-effort protection but cannot prevent DNS rebinding
 * attacks where a hostname returns a public IP during validation but a private
 * IP during the actual HTTP request. Full protection requires DNS pinning at
 * the HTTP client level.
 *
 * @licence GNU GPL v2+
 */
class UrlSsrfGuard {

	/**
	 * Returns true if the URL's hostname resolves to any private or reserved IP address.
	 * Also returns true (fail-closed) if the hostname cannot be resolved.
	 */
	public function urlResolvesToPrivateNetwork( string $url ): bool {
		$host = parse_url( $url, PHP_URL_HOST );

		if ( $host === false || $host === null ) {
			return true;
		}

		// Strip brackets from IPv6 literals (e.g. [::1])
		$host = trim( $host, '[]' );

		// Check if the host is an IP literal
		if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
			return !IPUtils::isPublic( $host );
		}

		// Resolve hostname to IPs (IPv4)
		$ipv4s = gethostbynamel( $host );

		// Also resolve IPv6 (AAAA records)
		$ipv6s = [];
		$aaaaRecords = dns_get_record( $host, DNS_AAAA );
		if ( is_array( $aaaaRecords ) ) {
			foreach ( $aaaaRecords as $record ) {
				if ( isset( $record['ipv6'] ) ) {
					$ipv6s[] = $record['ipv6'];
				}
			}
		}

		$allIps = array_merge( $ipv4s ?: [], $ipv6s );

		// Fail closed: if no IPs resolved, block the request
		if ( $allIps === [] ) {
			return true;
		}

		foreach ( $allIps as $ip ) {
			if ( !IPUtils::isPublic( $ip ) ) {
				return true;
			}
		}

		return false;
	}

}
