<?php

declare( strict_types = 1 );

namespace Maps\Config;

class StringType implements ConfigType {

	public function validate( mixed $value, string $location ): array {
		return is_string( $value ) ? [] : [ [ 'maps-config-error-invalid-string', $location ] ];
	}

}
