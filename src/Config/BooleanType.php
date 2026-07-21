<?php

declare( strict_types = 1 );

namespace Maps\Config;

class BooleanType implements ConfigType {

	public function validate( mixed $value, string $location ): array {
		return is_bool( $value ) ? [] : [ [ 'maps-config-error-invalid-boolean', $location ] ];
	}

	public function describe(): array {
		return [ 'maps-config-type-boolean' ];
	}

}
