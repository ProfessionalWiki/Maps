<?php

declare( strict_types = 1 );

namespace Maps\Config;

class EnumType implements ConfigType {

	/**
	 * @param string[] $allowedValues
	 */
	public function __construct(
		private array $allowedValues
	) {
	}

	public function validate( mixed $value, string $location ): array {
		if ( is_string( $value ) && in_array( $value, $this->allowedValues, true ) ) {
			return [];
		}

		return [ [ 'maps-config-error-invalid-enum', $location, implode( ', ', $this->allowedValues ) ] ];
	}

}
