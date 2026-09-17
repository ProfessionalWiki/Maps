<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\DataAccess;

use FileFetcher\FileFetchingException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Maps\DataAccess\GuardedFileFetcher;
use Maps\DataAccess\UrlSsrfGuard;
use Maps\Tests\TestDoubles\InMemoryHostResolver;
use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;

/**
 * Drives the fetcher through the HTTP client of MediaWiki, with a mock transport in place of curl.
 *
 * @covers \Maps\DataAccess\GuardedFileFetcher
 */
class GuardedFileFetcherTest extends TestCase {

	private const FILE_CONTENT = '{"type":"FeatureCollection","features":[]}';
	private const FILE_URL = 'http://example.com/f.geojson';
	private const PUBLIC_IPV4 = '93.184.216.34';

	private MockHandler $transport;

	protected function setUp(): void {
		$this->transport = new MockHandler( [ new Response( 200, [], self::FILE_CONTENT ) ] );
	}

	private function newFetcher(): GuardedFileFetcher {
		return new GuardedFileFetcher(
			MediaWikiServices::getInstance()->getHttpRequestFactory(),
			new UrlSsrfGuard( new InMemoryHostResolver( [ 'example.com' => [ self::PUBLIC_IPV4 ] ] ) ),
			$this->transport
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function optionsReachingTheTransport(): array {
		return $this->transport->getLastOptions();
	}

	public function testContentOfTheFileIsReturned(): void {
		$this->assertSame( self::FILE_CONTENT, $this->newFetcher()->fetchFile( self::FILE_URL ) );
	}

	public function testConnectionIsPinnedToTheCheckedAddress(): void {
		$this->newFetcher()->fetchFile( self::FILE_URL );

		$this->assertSame(
			[ 'example.com:80:' . self::PUBLIC_IPV4 ],
			$this->optionsReachingTheTransport()['curl'][CURLOPT_RESOLVE]
		);
	}

	public function testUrlThatIsFetchedIsTheOneTheGuardApproved(): void {
		$this->newFetcher()->fetchFile( 'http://127.0.0.1@EXAMPLE.com/f.geojson#fragment' );

		$this->assertSame( self::FILE_URL, (string)$this->transport->getLastRequest()->getUri() );
	}

	public function testRedirectsAreNotFollowed(): void {
		$this->newFetcher()->fetchFile( self::FILE_URL );

		$this->assertFalse( $this->optionsReachingTheTransport()['allow_redirects'] );
	}

	public function testIpLiteralIsFetchedWithoutAPin(): void {
		$this->newFetcher()->fetchFile( 'http://' . self::PUBLIC_IPV4 . '/f.geojson' );

		$this->assertArrayNotHasKey( CURLOPT_RESOLVE, $this->optionsReachingTheTransport()['curl'] ?? [] );
	}

	public function testUrlThatTheGuardRejectsIsNotFetched(): void {
		$this->transport = new MockHandler();

		$this->expectException( FileFetchingException::class );

		$this->newFetcher()->fetchFile( 'http://[::ffff:127.0.0.1]:8931/secret.geojson' );
	}

	public function testRequestThatFailsThrows(): void {
		$this->transport = new MockHandler( [ new Response( 404 ) ] );

		$this->expectException( FileFetchingException::class );

		$this->newFetcher()->fetchFile( self::FILE_URL );
	}

}
