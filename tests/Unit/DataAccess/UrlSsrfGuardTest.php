<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\ApprovedUrl;
use Maps\DataAccess\UrlSsrfGuard;
use Maps\Tests\TestDoubles\InMemoryHostResolver;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\UrlSsrfGuard
 */
class UrlSsrfGuardTest extends TestCase {

	private const PUBLIC_IPV4 = '93.184.216.34';
	private const PUBLIC_IPV6 = '2606:2800:220:1:248:1893:25c8:1946';

	/**
	 * Approves against a world in which example.com resolves to one public address.
	 */
	private function approve( string $url ): ?ApprovedUrl {
		return $this->approveWithDns( $url, [ 'example.com' => [ self::PUBLIC_IPV4 ] ] );
	}

	/**
	 * @param array<string, string[]> $addressesByHost
	 */
	private function approveWithDns( string $url, array $addressesByHost ): ?ApprovedUrl {
		return ( new UrlSsrfGuard( new InMemoryHostResolver( $addressesByHost ) ) )->approve( $url );
	}

	/**
	 * @dataProvider literalEmbeddingReservedIpv4Provider
	 */
	public function testLiteralEmbeddingAReservedIpv4IsNotApproved( string $url ): void {
		$this->assertNull( $this->approve( $url ) );
	}

	public static function literalEmbeddingReservedIpv4Provider(): iterable {
		yield 'IPv4-mapped loopback' => [ 'http://[::ffff:127.0.0.1]:8931/secret.geojson' ];
		yield 'IPv4-mapped loopback in hex' => [ 'http://[::ffff:7f00:1]/f.geojson' ];
		yield 'IPv4-mapped cloud metadata' => [ 'http://[::ffff:169.254.169.254]/latest/meta-data/' ];
		yield 'IPv4-mapped private address' => [ 'http://[::ffff:10.0.0.5]/f.geojson' ];
		yield 'NAT64 loopback' => [ 'http://[64:ff9b::7f00:1]/f.geojson' ];
		yield 'local-use NAT64 loopback' => [ 'http://[64:ff9b:1::7f00:1]/f.geojson' ];
		yield '6to4 loopback' => [ 'http://[2002:7f00:1::]/f.geojson' ];
	}

	/**
	 * @dataProvider literalEmbeddingPublicIpv4Provider
	 */
	public function testLiteralEmbeddingAPublicIpv4IsApproved( string $url ): void {
		$this->assertNotNull( $this->approve( $url ) );
	}

	public static function literalEmbeddingPublicIpv4Provider(): iterable {
		yield 'IPv4-mapped' => [ 'http://[::ffff:93.184.216.34]/f.geojson' ];
		yield 'NAT64' => [ 'http://[64:ff9b::5db8:d822]/f.geojson' ];
		yield '6to4' => [ 'http://[2002:5db8:d822::1]/f.geojson' ];
	}

	public function testUnspecifiedAddressIsNotApproved(): void {
		$this->assertNull( $this->approve( 'http://[::]/f.geojson' ) );
	}

	public function testReservedIpv4LiteralIsNotApproved(): void {
		$this->assertNull( $this->approve( 'http://127.0.0.1/f.geojson' ) );
	}

	public function testPublicIpv6LiteralKeepsItsBrackets(): void {
		$this->assertSame(
			'http://[2606:2800:220:1:248:1893:25c8:1946]/f.geojson',
			$this->approve( 'http://[2606:2800:220:1:248:1893:25c8:1946]/f.geojson' )->getUrl()
		);
	}

	public function testIpLiteralNeedsNoAddressesToPinTo(): void {
		$this->assertSame( [], $this->approve( 'http://93.184.216.34/f.geojson' )->getAddresses() );
	}

	/**
	 * @dataProvider unusableAnswerProvider
	 */
	public function testHostWithAnUnusableAnswerIsNotApproved( array $answers ): void {
		$this->assertNull(
			$this->approveWithDns( 'http://rebind.example/f.geojson', [ 'rebind.example' => $answers ] )
		);
	}

	public static function unusableAnswerProvider(): iterable {
		yield 'loopback' => [ [ '127.0.0.1' ] ];
		yield 'IPv4-mapped private address between public ones' => [
			[ self::PUBLIC_IPV4, '::ffff:10.0.0.5', self::PUBLIC_IPV6 ]
		];
		yield 'a name instead of an address' => [ [ 'localhost' ] ];
	}

	public function testHostThatDoesNotResolveIsNotApproved(): void {
		$this->assertNull( $this->approveWithDns( 'http://nowhere.example/f.geojson', [] ) );
	}

	public function testApprovedHostnameCarriesTheAddressesItResolvedTo(): void {
		$approvedUrl = $this->approveWithDns(
			'http://example.com/f.geojson',
			[ 'example.com' => [ self::PUBLIC_IPV4, self::PUBLIC_IPV6 ] ]
		);

		$this->assertSame( [ self::PUBLIC_IPV4, self::PUBLIC_IPV6 ], $approvedUrl->getAddresses() );
	}

	/**
	 * @dataProvider nonHttpUrlProvider
	 */
	public function testUrlWithoutAnHttpSchemeIsNotApproved( string $url ): void {
		$this->assertNull( $this->approve( $url ) );
	}

	public static function nonHttpUrlProvider(): iterable {
		yield 'ftp' => [ 'ftp://example.com/f.geojson' ];
		yield 'file' => [ 'file:///etc/passwd' ];
		yield 'gopher' => [ 'gopher://example.com/f.geojson' ];
		yield 'scheme relative' => [ '//example.com/f.geojson' ];
	}

	public function testUppercaseSchemeAndHostAreLowercased(): void {
		$this->assertSame(
			'http://example.com/File.geojson',
			$this->approve( 'HTTP://Example.COM/File.geojson' )->getUrl()
		);
	}

	/**
	 * The resolver would answer for each of these hosts, so only the host itself can be the reason.
	 *
	 * @dataProvider unusableHostProvider
	 */
	public function testHostThatIsNeitherAnIpLiteralNorAHostnameIsNotApproved( string $url, string $host ): void {
		$this->assertNull( $this->approveWithDns( $url, [ $host => [ self::PUBLIC_IPV4 ] ] ) );
	}

	public static function unusableHostProvider(): iterable {
		yield 'nothing but a scheme' => [ 'http://', '' ];
		yield 'hostname in brackets' => [ 'http://[example.com]/f.geojson', '[example.com]' ];
		yield 'trailing dot' => [ 'http://example.com./f.geojson', 'example.com.' ];
		yield 'underscore' => [ 'http://exa_mple.com/f.geojson', 'exa_mple.com' ];
		yield 'zone id' => [ 'http://[fe80::1%25eth0]/f.geojson', '[fe80::1%25eth0]' ];
		yield 'label starting with a hyphen' => [ 'http://-example.com/f.geojson', '-example.com' ];

		$overLongHost = str_repeat( 'a.', 130 ) . 'example';
		yield 'longer than a hostname may be' => [ 'http://' . $overLongHost . '/f.geojson', $overLongHost ];
	}

	/**
	 * curl reads these as 127.0.0.1 while PHP does not, so they are rejected on their form rather
	 * than on what they resolve to.
	 *
	 * @dataProvider numericHostProvider
	 */
	public function testNumericHostIsRejectedWithoutResolving( string $url, string $host ): void {
		$this->assertNull( $this->approveWithDns( $url, [ $host => [ self::PUBLIC_IPV4 ] ] ) );
	}

	public static function numericHostProvider(): iterable {
		yield 'decimal' => [ 'http://2130706433/f.geojson', '2130706433' ];
		yield 'hexadecimal' => [ 'http://0x7f000001/f.geojson', '0x7f000001' ];
		yield 'octal' => [ 'http://0177.0.0.1/f.geojson', '0177.0.0.1' ];
		yield 'short form' => [ 'http://127.1/f.geojson', '127.1' ];
	}

	public function testUserInfoIsDropped(): void {
		$this->assertSame(
			'http://example.com/f.geojson',
			$this->approve( 'http://127.0.0.1@example.com/f.geojson' )->getUrl()
		);
	}

	public function testHostBehindUserInfoDecidesWhetherTheUrlIsApproved(): void {
		$this->assertNull( $this->approve( 'http://example.com@127.0.0.1/f.geojson' ) );
	}

	public function testFragmentIsDropped(): void {
		$this->assertSame(
			'http://example.com/f.geojson',
			$this->approve( 'http://example.com/f.geojson#fragment' )->getUrl()
		);
	}

	public function testPortPathAndQueryAreKept(): void {
		$this->assertSame(
			'http://example.com:8931/dir/f.geojson?a=1&b=2',
			$this->approve( 'http://example.com:8931/dir/f.geojson?a=1&b=2' )->getUrl()
		);
	}

	public function testPortToConnectToIsTheOneInTheUrl(): void {
		$this->assertSame( 8931, $this->approve( 'http://example.com:8931/f.geojson' )->getPort() );
	}

	/**
	 * @dataProvider schemeDefaultPortProvider
	 */
	public function testPortToConnectToFallsBackToThePortOfTheScheme( string $url, int $expectedPort ): void {
		$this->assertSame( $expectedPort, $this->approve( $url )->getPort() );
	}

	public static function schemeDefaultPortProvider(): iterable {
		yield 'http' => [ 'http://example.com/f.geojson', 80 ];
		yield 'https' => [ 'https://example.com/f.geojson', 443 ];
	}

}
