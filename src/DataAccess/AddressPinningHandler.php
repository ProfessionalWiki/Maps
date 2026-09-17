<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Guzzle handler that pins the connection to the addresses the SSRF guard checked, so that curl
 * does not resolve the host a second time and connect to an address that was never checked.
 *
 * @licence GNU GPL v2+
 */
class AddressPinningHandler {

	private Closure $handler;

	public function __construct(
		callable $handler,
		private ApprovedUrl $url
	) {
		$this->handler = Closure::fromCallable( $handler );
	}

	public function __invoke( RequestInterface $request, array $options ): PromiseInterface {
		$options['curl'][CURLOPT_RESOLVE] = $this->addressPins();

		return ( $this->handler )( $request, $options );
	}

	/**
	 * The first address on its own, and then all of them.
	 *
	 * libcurl only understands an entry holding several addresses since 7.59, and silently ignores
	 * the whole entry before that, which would leave the connection unpinned. The single address
	 * entry keeps such a libcurl pinned, and a newer one replaces it with the full list, which it
	 * can fail over between.
	 *
	 * @return string[]
	 */
	private function addressPins(): array {
		$addresses = $this->bracketedAddresses();

		if ( count( $addresses ) === 1 ) {
			return [ $this->addressPin( $addresses ) ];
		}

		return [
			$this->addressPin( [ $addresses[0] ] ),
			$this->addressPin( $addresses ),
		];
	}

	/**
	 * @param string[] $addresses
	 */
	private function addressPin( array $addresses ): string {
		return $this->url->getHost() . ':' . $this->url->getPort() . ':' . implode( ',', $addresses );
	}

	/**
	 * @return string[]
	 */
	private function bracketedAddresses(): array {
		return array_map(
			static fn ( string $address ): string => str_contains( $address, ':' ) ? '[' . $address . ']' : $address,
			$this->url->getAddresses()
		);
	}

}
