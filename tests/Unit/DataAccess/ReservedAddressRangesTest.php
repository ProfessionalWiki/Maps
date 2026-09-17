<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\ReservedAddressRanges;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\ReservedAddressRanges
 */
class ReservedAddressRangesTest extends TestCase {

	/**
	 * @dataProvider reservedAddressProvider
	 */
	public function testReservedAddressesAreBlocked( string $address ): void {
		$this->assertTrue( ( new ReservedAddressRanges() )->blocks( $address ) );
	}

	public static function reservedAddressProvider(): iterable {
		yield 'this network' => [ '0.0.0.0' ];
		yield 'private 10/8' => [ '10.1.2.3' ];
		yield 'private 10/8, last address' => [ '10.255.255.255' ];
		yield 'carrier-grade NAT' => [ '100.64.0.1' ];
		yield 'carrier-grade NAT, last address' => [ '100.127.255.255' ];
		yield 'loopback' => [ '127.0.0.1' ];
		yield 'loopback, last address' => [ '127.255.255.254' ];
		yield 'link-local, where cloud metadata lives' => [ '169.254.169.254' ];
		yield 'private 172.16/12' => [ '172.20.1.1' ];
		yield 'private 172.16/12, last address' => [ '172.31.255.255' ];
		yield 'IETF protocol assignments' => [ '192.0.0.171' ];
		yield 'documentation 192.0.2/24' => [ '192.0.2.5' ];
		yield '6to4 relay anycast' => [ '192.88.99.1' ];
		yield 'private 192.168/16' => [ '192.168.1.1' ];
		yield 'private 192.168/16, last address' => [ '192.168.255.255' ];
		yield 'benchmarking' => [ '198.19.0.1' ];
		yield 'documentation 198.51.100/24' => [ '198.51.100.5' ];
		yield 'documentation 203.0.113/24' => [ '203.0.113.5' ];
		yield 'multicast' => [ '239.255.255.250' ];
		yield 'broadcast' => [ '255.255.255.255' ];

		yield 'unspecified' => [ '::' ];
		yield 'IPv6 loopback' => [ '::1' ];
		yield 'local-use NAT64' => [ '64:ff9b:1::1' ];
		yield 'discard-only' => [ '100::1' ];
		yield 'Teredo' => [ '2001:0:1234::1' ];
		yield 'IPv6 documentation' => [ '2001:db8::1' ];
		yield 'IPv6 documentation, second range' => [ '3fff::1' ];
		yield 'IPv6 IETF protocol assignments' => [ '2001:1ff::1' ];
		yield 'unique local' => [ 'fd00::1' ];
		yield 'unique local, last address' => [ 'fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' ];
		yield 'IPv6 link-local' => [ 'fe80::1' ];
		yield 'IPv6 link-local, last address' => [ 'febf::1' ];
		yield 'site-local' => [ 'fec0::1' ];
		yield 'IPv6 multicast' => [ 'ff02::1' ];

		yield 'IPv4-mapped loopback' => [ '::ffff:127.0.0.1' ];
		yield 'IPv4-mapped cloud metadata, written in hex' => [ '::ffff:a9fe:a9fe' ];
		yield 'IPv4-compatible loopback' => [ '::7f00:1' ];
		yield 'NAT64 loopback' => [ '64:ff9b::7f00:1' ];
		yield '6to4 private' => [ '2002:a00:5::1' ];

		yield 'outside global unicast, unassigned' => [ 'fe00::1' ];
		yield 'outside global unicast, segment routing' => [ '5f00::1' ];
		yield 'outside global unicast, deprecated NSAP mapping' => [ '200::1' ];
	}

	/**
	 * @dataProvider publiclyRoutableAddressProvider
	 */
	public function testPubliclyRoutableAddressesAreNotBlocked( string $address ): void {
		$this->assertFalse( ( new ReservedAddressRanges() )->blocks( $address ) );
	}

	public static function publiclyRoutableAddressProvider(): iterable {
		yield 'IPv4' => [ '93.184.216.34' ];
		yield 'IPv4 just below the link-local range' => [ '169.253.255.255' ];
		yield 'IPv4 just above the 172.16/12 range' => [ '172.32.0.1' ];
		yield 'IPv4 just below the 10/8 range' => [ '9.255.255.255' ];
		yield 'IPv4 just above the 10/8 range' => [ '11.0.0.1' ];
		yield 'IPv4 just above the carrier-grade NAT range' => [ '100.128.0.1' ];
		yield 'IPv4 just above the benchmarking range' => [ '198.20.0.1' ];
		yield 'IPv6' => [ '2606:2800:220:1:248:1893:25c8:1946' ];
		yield 'IPv6 just above the IETF protocol assignments' => [ '2001:200::1' ];
		yield 'IPv6 just above the documentation range' => [ '2001:db9::1' ];
		yield 'IPv4-mapped public address' => [ '::ffff:93.184.216.34' ];
		yield 'NAT64 public address' => [ '64:ff9b::5db8:d822' ];
		yield '6to4 public address' => [ '2002:5db8:d822::1' ];
	}

	public function testValueThatIsNotAnAddressIsBlocked(): void {
		$this->assertTrue( ( new ReservedAddressRanges() )->blocks( 'example.com' ) );
	}

}
