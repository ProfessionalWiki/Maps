<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Maps\DataAccess\AddressPinningHandler;
use Maps\DataAccess\ApprovedUrl;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Maps\DataAccess\AddressPinningHandler
 */
class AddressPinningHandlerTest extends TestCase {

	private const URL = 'https://example.com/f.geojson';
	private const PUBLIC_IPV4 = '93.184.216.34';
	private const PUBLIC_IPV6 = '2606:2800:220:1:248:1893:25c8:1946';

	private MockHandler $transport;

	protected function setUp(): void {
		$this->transport = new MockHandler( [ new Response( 200 ) ] );
	}

	/**
	 * @param string[] $addresses
	 * @param array<string, mixed> $options
	 */
	private function sendThroughPin( array $addresses, array $options = [] ): ResponseInterface {
		$handler = new AddressPinningHandler(
			$this->transport,
			new ApprovedUrl( self::URL, 'example.com', 443, $addresses )
		);

		return $handler( new Request( 'GET', self::URL ), $options )->wait();
	}

	/**
	 * @return array<string, mixed>
	 */
	private function optionsReachingTheTransport(): array {
		return $this->transport->getLastOptions();
	}

	public function testConnectionIsPinnedToTheCheckedAddress(): void {
		$this->sendThroughPin( [ self::PUBLIC_IPV4 ] );

		$this->assertSame(
			[ 'example.com:443:' . self::PUBLIC_IPV4 ],
			$this->optionsReachingTheTransport()['curl'][CURLOPT_RESOLVE]
		);
	}

	/**
	 * The single address entry comes first for the libcurl versions that ignore an entry holding
	 * several addresses, and a newer libcurl replaces it with the full list.
	 */
	public function testEveryCheckedAddressIsPinned(): void {
		$this->sendThroughPin( [ self::PUBLIC_IPV4, self::PUBLIC_IPV6 ] );

		$this->assertSame(
			[
				'example.com:443:' . self::PUBLIC_IPV4,
				'example.com:443:' . self::PUBLIC_IPV4 . ',[' . self::PUBLIC_IPV6 . ']',
			],
			$this->optionsReachingTheTransport()['curl'][CURLOPT_RESOLVE]
		);
	}

	public function testOtherCurlOptionsAreKept(): void {
		$this->sendThroughPin( [ self::PUBLIC_IPV4 ], [ 'curl' => [ CURLOPT_TIMEOUT => 7 ] ] );

		$this->assertSame( 7, $this->optionsReachingTheTransport()['curl'][CURLOPT_TIMEOUT] );
	}

	public function testResponseOfTheTransportIsReturned(): void {
		$this->transport = new MockHandler( [ new Response( 418 ) ] );

		$this->assertSame( 418, $this->sendThroughPin( [ self::PUBLIC_IPV4 ] )->getStatusCode() );
	}

}
