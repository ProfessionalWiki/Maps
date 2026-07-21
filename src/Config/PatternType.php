<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * A string that must match a regular expression. Used where the value feeds into a security
 * sensitive context, such as the Google Maps interface language which is placed in the API URL.
 */
class PatternType implements ConfigType {

	public function __construct(
		private string $pattern,
		private string $errorMessageKey,
		private string $descriptionMessageKey
	) {
	}

	public function validate( mixed $value, string $location ): array {
		if ( is_string( $value ) && preg_match( $this->pattern, $value ) === 1 ) {
			return [];
		}

		return [ [ $this->errorMessageKey, $location ] ];
	}

	public function describe(): array {
		return [ $this->descriptionMessageKey ];
	}

}
