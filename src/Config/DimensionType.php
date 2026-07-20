<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * A CSS dimension such as a map width or height, mirroring what the width and height parameters
 * accept: a non-negative number, optionally with one of the allowed units, or "auto" when allowed.
 * The value can end up in an inline style attribute, so the strict format also keeps it safe there.
 */
class DimensionType implements ConfigType {

	/**
	 * @param string[] $units Allowed unit suffixes, such as [ 'px', 'em', '%' ]. A bare number is
	 *        always allowed.
	 */
	public function __construct(
		private array $units,
		private bool $allowAuto
	) {
	}

	public function validate( mixed $value, string $location ): array {
		return $this->isValid( $value ) ? [] : [ [ 'maps-config-error-invalid-dimension', $location ] ];
	}

	private function isValid( mixed $value ): bool {
		if ( is_int( $value ) || is_float( $value ) ) {
			return $value >= 0;
		}

		if ( !is_string( $value ) ) {
			return false;
		}

		if ( $this->allowAuto && $value === 'auto' ) {
			return true;
		}

		return preg_match( $this->dimensionRegex(), $value ) === 1;
	}

	private function dimensionRegex(): string {
		$units = array_filter( $this->units, static fn ( string $unit ): bool => $unit !== '' );

		$unitGroup = $units === []
			? ''
			: '(' . implode( '|', array_map( 'preg_quote', $units ) ) . ')?';

		// The D modifier keeps $ from matching before a trailing newline, so a value with one is
		// rejected rather than reaching an inline style attribute.
		return '/^\d+(\.\d+)?' . $unitGroup . '$/D';
	}

}
