<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * A URL the SSRF guard approved for fetching, rebuilt from the parts the guard checked.
 *
 * @licence GNU GPL v2+
 */
class ApprovedUrl {

	/**
	 * @param string $host The host as it appears in the URL, so an IPv6 literal keeps its brackets
	 * @param int $port The port to connect to, which is the default port of the scheme when the URL has none
	 * @param string[] $addresses The checked addresses of the host, empty when the host is an IP literal
	 */
	public function __construct(
		private string $url,
		private string $host,
		private int $port,
		private array $addresses
	) {
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getHost(): string {
		return $this->host;
	}

	public function getPort(): int {
		return $this->port;
	}

	/**
	 * @return string[]
	 */
	public function getAddresses(): array {
		return $this->addresses;
	}

}
