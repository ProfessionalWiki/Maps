<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * How a wiki value combines with the PHP value for a setting: it either replaces it wholesale, or,
 * for the layer definition and availability maps, is merged per name so the wiki only overrides the
 * named entries it lists.
 */
enum MergeStrategy {

	case Replace;
	case Union;

	public function combine( mixed $phpValue, mixed $wikiValue ): mixed {
		if ( $this === self::Union && is_array( $phpValue ) && is_array( $wikiValue ) ) {
			// Union rather than array_merge: array_merge renumbers integer-like keys, which would
			// drop an entry whose name is purely numeric. Wiki entries win on collision.
			return $wikiValue + $phpValue;
		}

		return $wikiValue;
	}

}
