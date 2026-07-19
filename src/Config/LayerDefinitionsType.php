<?php

declare( strict_types = 1 );

namespace Maps\Config;

use Maps\LeafletLayerContract;

/**
 * The custom Leaflet layer definitions map. Applies the same rules as the render-time hardening in
 * LeafletLayerDefinitions (both build on LeafletLayerContract): valid layer names, http(s) urls,
 * only allowlisted Leaflet options, and a boolean wms flag. Merged per name with the PHP settings.
 */
class LayerDefinitionsType implements ConfigType {

	private const DEFINITION_KEYS = [ 'url', 'options', 'wms' ];

	public function validate( mixed $value, string $location ): array {
		if ( !$this->isObject( $value ) ) {
			return [ [ 'maps-config-error-not-object', $location ] ];
		}

		$errors = [];

		foreach ( $value as $name => $definition ) {
			$name = (string)$name;

			if ( LeafletLayerContract::isValidLayerName( $name ) ) {
				$errors = array_merge( $errors, $this->validateDefinition( $name, $definition ) );
			} else {
				$errors[] = [ 'maps-config-error-invalid-layer-name', $name ];
			}
		}

		return $errors;
	}

	private function validateDefinition( string $name, mixed $definition ): array {
		if ( !$this->isObject( $definition ) ) {
			return [ [ 'maps-config-error-not-object', $name ] ];
		}

		$errors = [];

		foreach ( array_keys( $definition ) as $key ) {
			if ( !in_array( $key, self::DEFINITION_KEYS, true ) ) {
				$errors[] = [ 'maps-config-error-unknown-layer-key', $name, (string)$key ];
			}
		}

		$url = $definition['url'] ?? null;
		if ( !is_string( $url ) || !LeafletLayerContract::isValidLayerUrl( $url ) ) {
			$errors[] = [ 'maps-config-error-invalid-url', $name ];
		}

		if ( array_key_exists( 'wms', $definition ) && !is_bool( $definition['wms'] ) ) {
			$errors[] = [ 'maps-config-error-invalid-wms', $name ];
		}

		if ( array_key_exists( 'options', $definition ) ) {
			$errors = array_merge(
				$errors,
				$this->validateOptions( $name, $definition['options'], ( $definition['wms'] ?? false ) === true )
			);
		}

		return $errors;
	}

	private function validateOptions( string $name, mixed $options, bool $wms ): array {
		if ( !$this->isObject( $options ) ) {
			return [ [ 'maps-config-error-not-object', $name . '.options' ] ];
		}

		$allowed = array_fill_keys( LeafletLayerContract::allowedOptions( $wms ), true );

		$errors = [];

		foreach ( $options as $key => $value ) {
			if ( !isset( $allowed[$key] ) ) {
				$errors[] = [ 'maps-config-error-unknown-option', $name, (string)$key ];
			} elseif ( $key === 'attribution' && !is_string( $value ) ) {
				$errors[] = [ 'maps-config-error-invalid-attribution', $name ];
			} elseif ( in_array( $key, LeafletLayerContract::URL_OPTIONS, true )
				&& ( !is_string( $value ) || !LeafletLayerContract::isValidLayerUrl( $value ) ) ) {
				$errors[] = [ 'maps-config-error-invalid-option-url', $name, (string)$key ];
			}
		}

		return $errors;
	}

	private function isObject( mixed $value ): bool {
		return is_array( $value ) && ( $value === [] || !array_is_list( $value ) );
	}

}
