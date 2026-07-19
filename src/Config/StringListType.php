<?php

declare( strict_types = 1 );

namespace Maps\Config;

class StringListType implements ConfigType {

	public function validate( mixed $value, string $location ): array {
		return $this->isStringList( $value ) ? [] : [ [ 'maps-config-error-invalid-default-list', $location ] ];
	}

	private function isStringList( mixed $value ): bool {
		if ( !is_array( $value ) || !array_is_list( $value ) ) {
			return false;
		}

		foreach ( $value as $item ) {
			if ( !is_string( $item ) ) {
				return false;
			}
		}

		return true;
	}

}
