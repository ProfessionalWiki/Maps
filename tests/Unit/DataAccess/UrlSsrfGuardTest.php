<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\UrlSsrfGuard;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\UrlSsrfGuard
 */
class UrlSsrfGuardTest extends TestCase {

	private UrlSsrfGuard $guard;

	protected function setUp(): void {
		$this->guard = new UrlSsrfGuard();
	}

	/**
	 * @dataProvider privateIpUrlProvider
	 */
	public function testPrivateIpUrlsAreBlocked( string $url ): void {
		$this->assertTrue(
			$this->guard->urlResolvesToPrivateNetwork( $url ),
			"Expected $url to be blocked as private network"
		);
	}

	public static function privateIpUrlProvider(): iterable {
		// IPv4 loopback
		yield 'loopback' => [ 'http://127.0.0.1/file' ];
		yield 'loopback other' => [ 'http://127.0.0.2/file' ];

		// AWS metadata / link-local
		yield 'AWS metadata' => [ 'http://169.254.169.254/latest/meta-data/' ];
		yield 'link-local' => [ 'http://169.254.1.1/file' ];

		// RFC 1918 private ranges
		yield 'private 10.x' => [ 'http://10.0.0.1/file' ];
		yield 'private 10.x high' => [ 'http://10.255.255.255/file' ];
		yield 'private 172.16.x' => [ 'http://172.16.0.1/file' ];
		yield 'private 172.31.x' => [ 'http://172.31.255.255/file' ];
		yield 'private 192.168.x' => [ 'http://192.168.1.1/file' ];

		// "This network" (0.0.0.0/8)
		yield 'zero address' => [ 'http://0.0.0.0/file' ];

		// IPv6 loopback
		yield 'IPv6 loopback' => [ 'http://[::1]/file' ];

		// IPv6 private (fc00::/7)
		yield 'IPv6 ULA' => [ 'http://[fd00::1]/file' ];
		yield 'IPv6 ULA fc' => [ 'http://[fc00::1]/file' ];

		// IPv6 link-local (fe80::/10)
		yield 'IPv6 link-local' => [ 'http://[fe80::1]/file' ];

		// Hostname resolving to loopback
		yield 'localhost' => [ 'http://localhost/file' ];
	}

	/**
	 * @dataProvider invalidUrlProvider
	 */
	public function testInvalidUrlsAreBlocked( string $url ): void {
		$this->assertTrue(
			$this->guard->urlResolvesToPrivateNetwork( $url ),
			"Expected invalid URL $url to be blocked (fail-closed)"
		);
	}

	public static function invalidUrlProvider(): iterable {
		yield 'no host' => [ 'http://' ];
		yield 'unresolvable host' => [ 'http://this-domain-does-not-exist-xyzzy.invalid/file' ];
	}

	public function testPublicIpUrlIsAllowed(): void {
		// 93.184.216.34 is example.com's well-known public IP
		$this->assertFalse(
			$this->guard->urlResolvesToPrivateNetwork( 'http://93.184.216.34/file' ),
			'Public IP should not be blocked'
		);
	}

	public function testPublicHostnameIsAllowed(): void {
		$this->assertFalse(
			$this->guard->urlResolvesToPrivateNetwork( 'http://example.com/file' ),
			'Public hostname should not be blocked'
		);
	}
}
