<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * The IP addresses the wiki must not fetch from: loopback, private, link-local, carrier-grade NAT,
 * documentation, benchmarking, multicast and other reserved space, in both address families.
 *
 * An IPv6 address that embeds an IPv4 address is judged by that address. Any other IPv6 address
 * must be in the global unicast space that addresses on the internet come from, which leaves out
 * the ranges the IETF has not handed out and that a network can thus use internally.
 *
 * @licence GNU GPL v2+
 */
class ReservedAddressRanges {

	private const IPV4_RANGES = [
		'0.0.0.0/8',
		'10.0.0.0/8',
		'100.64.0.0/10',
		'127.0.0.0/8',
		'169.254.0.0/16',
		'172.16.0.0/12',
		'192.0.0.0/24',
		'192.0.2.0/24',
		'192.88.99.0/24',
		'192.168.0.0/16',
		'198.18.0.0/15',
		'198.51.100.0/24',
		'203.0.113.0/24',
		'224.0.0.0/4',
		'240.0.0.0/4',
	];

	/**
	 * Named for what they are, even where the global unicast rule below already leaves them out.
	 */
	private const IPV6_RANGES = [
		'::/128',
		'::1/128',
		'64:ff9b:1::/48',
		'100::/64',
		'2001::/23',
		'2001:db8::/32',
		'3fff::/20',
		'fc00::/7',
		'fe80::/10',
		'fec0::/10',
		'ff00::/8',
	];

	/**
	 * IPv6 ranges that embed an IPv4 address, each with the byte offset of that address.
	 */
	private const IPV4_EMBEDDING_RANGES = [
		'::ffff:0:0/96' => 12,
		'64:ff9b::/96' => 12,
		'2002::/16' => 2,
	];

	private const IPV6_GLOBAL_UNICAST_RANGE = '2000::/3';

	/**
	 * Anything that is not an IP address counts as reserved, so that callers fail closed.
	 */
	public function blocks( string $address ): bool {
		$packedAddress = inet_pton( $address );

		if ( $packedAddress === false ) {
			return true;
		}

		if ( strlen( $packedAddress ) === 4 ) {
			return $this->inAnyRange( $packedAddress, self::IPV4_RANGES );
		}

		if ( $this->inAnyRange( $packedAddress, self::IPV6_RANGES ) ) {
			return true;
		}

		$embeddedIpv4 = $this->embeddedIpv4( $packedAddress );

		if ( $embeddedIpv4 !== null ) {
			return $this->inAnyRange( $embeddedIpv4, self::IPV4_RANGES );
		}

		return !$this->inRange( $packedAddress, self::IPV6_GLOBAL_UNICAST_RANGE );
	}

	/**
	 * @param string[] $ranges
	 */
	private function inAnyRange( string $packedAddress, array $ranges ): bool {
		foreach ( $ranges as $range ) {
			if ( $this->inRange( $packedAddress, $range ) ) {
				return true;
			}
		}

		return false;
	}

	private function inRange( string $packedAddress, string $range ): bool {
		[ $rangeAddress, $prefixLength ] = explode( '/', $range );

		$packedRange = (string)inet_pton( $rangeAddress );
		$wholeBytes = intdiv( (int)$prefixLength, 8 );
		$remainingBits = (int)$prefixLength % 8;

		if ( strncmp( $packedAddress, $packedRange, $wholeBytes ) !== 0 ) {
			return false;
		}

		if ( $remainingBits === 0 ) {
			return true;
		}

		$mask = 0xff << ( 8 - $remainingBits ) & 0xff;

		return ( ord( $packedAddress[$wholeBytes] ) & $mask ) === ( ord( $packedRange[$wholeBytes] ) & $mask );
	}

	/**
	 * The packed IPv4 address the given packed IPv6 address embeds, or null when it embeds none.
	 */
	private function embeddedIpv4( string $packedAddress ): ?string {
		foreach ( self::IPV4_EMBEDDING_RANGES as $range => $offset ) {
			if ( $this->inRange( $packedAddress, $range ) ) {
				return substr( $packedAddress, $offset, 4 );
			}
		}

		return null;
	}

}
