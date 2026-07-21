<?php

declare( strict_types = 1 );

namespace Maps\Config;

class EnumListType implements ConfigType {

	/**
	 * @param string[] $allowedValues
	 */
	public function __construct(
		private array $allowedValues
	) {
	}

	public function validate( mixed $value, string $location ): array {
		if ( $this->isValid( $value ) ) {
			return [];
		}

		return [ [ 'maps-config-error-invalid-enum-list', $location, implode( ', ', $this->allowedValues ) ] ];
	}

	public function describe(): array {
		return [ 'maps-config-type-enum-list', implode( ', ', $this->allowedValues ) ];
	}

	private function isValid( mixed $value ): bool {
		if ( !is_array( $value ) || !array_is_list( $value ) ) {
			return false;
		}

		foreach ( $value as $item ) {
			if ( !is_string( $item ) || !in_array( $item, $this->allowedValues, true ) ) {
				return false;
			}
		}

		return true;
	}

}
