<?php

declare( strict_types = 1 );

namespace Maps\Config;

class IntegerType implements ConfigType {

	public function __construct(
		private ?int $minimum = null
	) {
	}

	public function validate( mixed $value, string $location ): array {
		if ( !is_int( $value ) ) {
			return [ [ 'maps-config-error-invalid-integer', $location ] ];
		}

		if ( $this->minimum !== null && $value < $this->minimum ) {
			return [ [ 'maps-config-error-integer-too-small', $location, $this->minimum ] ];
		}

		return [];
	}

}
