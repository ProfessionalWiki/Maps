<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use Closure;
use Exception;
use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\Http\HttpRequestFactory;

/**
 * Fetches files from URLs that editors provide, such as the GeoJSON sources of a map.
 *
 * Only URLs approved by the SSRF guard are fetched, with the connection pinned to the addresses the
 * guard checked. Pinning is a curl option, so without the curl extension the fetch is refused rather
 * than made without a pin. A configured HTTP proxy resolves the host itself, which no pin reaches.
 *
 * @licence GNU GPL v2+
 */
class GuardedFileFetcher implements FileFetcher {

	private Closure $transport;

	public function __construct(
		private HttpRequestFactory $httpRequestFactory,
		private UrlSsrfGuard $ssrfGuard,
		callable $transport
	) {
		$this->transport = Closure::fromCallable( $transport );
	}

	public function fetchFile( string $fileUrl ): string {
		if ( !extension_loaded( 'curl' ) ) {
			throw new FileFetchingException( $fileUrl );
		}

		$approvedUrl = $this->ssrfGuard->approve( $fileUrl );

		if ( $approvedUrl === null ) {
			throw new FileFetchingException( $fileUrl );
		}

		return $this->fetchApprovedUrl( $approvedUrl, $fileUrl );
	}

	/**
	 * @param string $requestedUrl The URL the caller asked for, which the exception names
	 */
	private function fetchApprovedUrl( ApprovedUrl $approvedUrl, string $requestedUrl ): string {
		try {
			$request = $this->httpRequestFactory->create(
				$approvedUrl->getUrl(),
				[
					'method' => 'GET',
					'followRedirects' => false,
					'handler' => $this->newHandler( $approvedUrl ),
				],
				__METHOD__
			);

			$requestSucceeded = $request->execute()->isOK();
		}
		catch ( Exception $ex ) {
			throw new FileFetchingException( $requestedUrl, null, $ex );
		}

		if ( !$requestSucceeded ) {
			throw new FileFetchingException( $requestedUrl );
		}

		return $request->getContent();
	}

	private function newHandler( ApprovedUrl $approvedUrl ): callable {
		if ( $approvedUrl->getAddresses() === [] ) {
			return $this->transport;
		}

		return new AddressPinningHandler( $this->transport, $approvedUrl );
	}

}
