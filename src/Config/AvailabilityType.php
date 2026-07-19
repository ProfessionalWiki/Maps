<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * A map of layer names to booleans, used for the available base layers and overlays. Merged per
 * name with the PHP settings rather than replaced, so the wiki only needs to list the layers it
 * changes.
 */
class AvailabilityType implements ConfigType {

	public function validate( mixed $value, string $location ): array {
		return $this->isAvailabilityMap( $value ) ? [] : [ [ 'maps-config-error-invalid-availability', $location ] ];
	}

	private function isAvailabilityMap( mixed $value ): bool {
		if ( !is_array( $value ) || ( $value !== [] && array_is_list( $value ) ) ) {
			return false;
		}

		foreach ( $value as $enabled ) {
			if ( !is_bool( $enabled ) ) {
				return false;
			}
		}

		return true;
	}

}
