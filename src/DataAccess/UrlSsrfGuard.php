<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * Decides whether the wiki may fetch a URL an editor provided, to prevent Server-Side Request
 * Forgery (SSRF).
 *
 * Only http and https URLs are approved, and only when the host is an IP literal or a hostname as
 * strict as RFC 1123 allows, and neither the literal nor any address the host resolves to is
 * reserved. The approved URL is rebuilt from the parts that were checked, so that the fetch cannot
 * end up at a host other than the checked one.
 *
 * @licence GNU GPL v2+
 */
class UrlSsrfGuard {

	private const SCHEME_PORTS = [
		'http' => 80,
		'https' => 443,
	];

	private const MAX_HOSTNAME_LENGTH = 253;

	/**
	 * ASCII labels of letters, digits and hyphens, with a top level label that starts with a letter.
	 * RFC 1123 section 2.1 guarantees the latter, and it keeps out the numeric hosts that PHP,
	 * Guzzle and curl do not all read as the same address, such as 2130706433 and 127.1.
	 */
	private const HOSTNAME_PATTERN = '/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z]([a-z0-9-]{0,61}[a-z0-9])?$/D';

	private ReservedAddressRanges $reservedRanges;

	public function __construct(
		private HostResolver $hostResolver
	) {
		$this->reservedRanges = new ReservedAddressRanges();
	}

	/**
	 * Returns what to fetch, or null when the URL must not be fetched.
	 */
	public function approve( string $url ): ?ApprovedUrl {
		$parts = parse_url( $url );

		if ( $parts === false ) {
			return null;
		}

		$scheme = strtolower( $parts['scheme'] ?? '' );

		if ( !array_key_exists( $scheme, self::SCHEME_PORTS ) ) {
			return null;
		}

		$host = strtolower( $parts['host'] ?? '' );
		$addresses = $this->allowedAddresses( $host );

		if ( $addresses === null ) {
			return null;
		}

		return new ApprovedUrl(
			$this->buildUrl( $scheme, $host, $parts ),
			$host,
			$parts['port'] ?? self::SCHEME_PORTS[$scheme],
			$addresses
		);
	}

	/**
	 * The addresses the host may be connected to: empty for an IP literal, which is the address
	 * itself, and null when the host may not be connected to at all.
	 *
	 * @return string[]|null
	 */
	private function allowedAddresses( string $host ): ?array {
		$literalAddress = $this->ipLiteralAddress( $host );

		if ( $literalAddress !== null ) {
			return $this->reservedRanges->blocks( $literalAddress ) ? null : [];
		}

		if ( !$this->isHostname( $host ) ) {
			return null;
		}

		$addresses = $this->hostResolver->resolve( $host );

		if ( $addresses === [] ) {
			return null;
		}

		foreach ( $addresses as $address ) {
			if ( $this->reservedRanges->blocks( $address ) ) {
				return null;
			}
		}

		return $addresses;
	}

	/**
	 * The address of an IP literal host, or null when the host is not one.
	 */
	private function ipLiteralAddress( string $host ): ?string {
		if ( str_starts_with( $host, '[' ) && str_ends_with( $host, ']' ) ) {
			$address = substr( $host, 1, -1 );

			return filter_var( $address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) === false ? null : $address;
		}

		return filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === false ? null : $host;
	}

	private function isHostname( string $host ): bool {
		return strlen( $host ) <= self::MAX_HOSTNAME_LENGTH
			&& preg_match( self::HOSTNAME_PATTERN, $host ) === 1;
	}

	/**
	 * Keeps the port, path and query, and drops the user info and the fragment.
	 *
	 * @param array<string, mixed> $parts
	 */
	private function buildUrl( string $scheme, string $host, array $parts ): string {
		return $scheme . '://' . $host
			. ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' )
			. ( $parts['path'] ?? '' )
			. ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
	}

}
