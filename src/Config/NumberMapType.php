<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * A map of names to positive numbers, used for the distance units. The names must be alphanumeric
 * (starting with a letter) because they are both concatenated into a regular expression and shown
 * in rendered distances, so allowing arbitrary characters would be unsafe.
 */
class NumberMapType implements ConfigType {

	private const NAME_PATTERN = '/^[A-Za-z][A-Za-z0-9]*$/';

	public function validate( mixed $value, string $location ): array {
		return $this->isValid( $value ) ? [] : [ [ 'maps-config-error-invalid-number-map', $location ] ];
	}

	private function isValid( mixed $value ): bool {
		if ( !is_array( $value ) || $value === [] || array_is_list( $value ) ) {
			return false;
		}

		foreach ( $value as $name => $number ) {
			if ( preg_match( self::NAME_PATTERN, (string)$name ) !== 1 ) {
				return false;
			}

			if ( ( !is_int( $number ) && !is_float( $number ) ) || $number <= 0 ) {
				return false;
			}
		}

		return true;
	}

}
