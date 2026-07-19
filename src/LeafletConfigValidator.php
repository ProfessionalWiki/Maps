<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * Validates the JSON of the MediaWiki:Maps config page at save time, producing precise error
 * messages. The schema is strict: unknown keys and options are rejected, mirroring the render-time
 * hardening in LeafletLayerDefinitions (both build on LeafletLayerContract).
 *
 * Errors are returned as message specs: an array whose first element is a message key and whose
 * remaining elements are the message parameters. An empty result means the config is valid.
 *
 * @licence GNU GPL v2+
 */
class LeafletConfigValidator {

	public const ERROR_INVALID_JSON = 'maps-config-error-invalid-json';
	public const ERROR_NOT_OBJECT = 'maps-config-error-not-object';
	public const ERROR_UNKNOWN_KEY = 'maps-config-error-unknown-key';
	public const ERROR_INVALID_LAYER_NAME = 'maps-config-error-invalid-layer-name';
	public const ERROR_UNKNOWN_LAYER_KEY = 'maps-config-error-unknown-layer-key';
	public const ERROR_INVALID_URL = 'maps-config-error-invalid-url';
	public const ERROR_INVALID_WMS = 'maps-config-error-invalid-wms';
	public const ERROR_UNKNOWN_OPTION = 'maps-config-error-unknown-option';
	public const ERROR_INVALID_OPTION_URL = 'maps-config-error-invalid-option-url';
	public const ERROR_INVALID_ATTRIBUTION = 'maps-config-error-invalid-attribution';
	public const ERROR_INVALID_DEFAULT_LIST = 'maps-config-error-invalid-default-list';
	public const ERROR_INVALID_AVAILABILITY = 'maps-config-error-invalid-availability';

	private const LEAFLET_KEYS = [
		'layerDefinitions',
		'defaultLayers',
		'defaultOverlays',
		'availableLayers',
		'availableOverlays',
	];

	private const DEFINITION_KEYS = [ 'url', 'options', 'wms' ];

	/**
	 * @return array[] List of message specs, each [ messageKey, ...params ]. Empty when valid.
	 */
	public function validate( string $json ): array {
		$data = json_decode( $json, true );

		if ( $data === null ) {
			// Invalid JSON syntax is enforced by MediaWiki core for the JSON content model, so it
			// is left to core. Only a literal null value is flagged here.
			return trim( $json ) === 'null' ? [ [ self::ERROR_INVALID_JSON ] ] : [];
		}

		if ( !$this->isObject( $data ) ) {
			return [ [ self::ERROR_INVALID_JSON ] ];
		}

		return $this->validateTopLevel( $data );
	}

	private function validateTopLevel( array $data ): array {
		$errors = $this->unknownKeyErrors( $data, [ 'leaflet' ] );

		if ( array_key_exists( 'leaflet', $data ) ) {
			$errors = array_merge( $errors, $this->validateLeaflet( $data['leaflet'] ) );
		}

		return $errors;
	}

	private function validateLeaflet( mixed $leaflet ): array {
		if ( !$this->isObject( $leaflet ) ) {
			return [ [ self::ERROR_NOT_OBJECT, 'leaflet' ] ];
		}

		$errors = $this->unknownKeyErrors( $leaflet, self::LEAFLET_KEYS );

		if ( array_key_exists( 'layerDefinitions', $leaflet ) ) {
			$errors = array_merge( $errors, $this->validateLayerDefinitions( $leaflet['layerDefinitions'] ) );
		}

		foreach ( [ 'defaultLayers', 'defaultOverlays' ] as $key ) {
			if ( array_key_exists( $key, $leaflet ) && !$this->isStringList( $leaflet[$key] ) ) {
				$errors[] = [ self::ERROR_INVALID_DEFAULT_LIST, $key ];
			}
		}

		foreach ( [ 'availableLayers', 'availableOverlays' ] as $key ) {
			if ( array_key_exists( $key, $leaflet ) && !$this->isAvailabilityMap( $leaflet[$key] ) ) {
				$errors[] = [ self::ERROR_INVALID_AVAILABILITY, $key ];
			}
		}

		return $errors;
	}

	private function validateLayerDefinitions( mixed $definitions ): array {
		if ( !$this->isObject( $definitions ) ) {
			return [ [ self::ERROR_NOT_OBJECT, 'layerDefinitions' ] ];
		}

		$errors = [];

		foreach ( $definitions as $name => $definition ) {
			$name = (string)$name;

			if ( LeafletLayerContract::isValidLayerName( $name ) ) {
				$errors = array_merge( $errors, $this->validateDefinition( $name, $definition ) );
			} else {
				$errors[] = [ self::ERROR_INVALID_LAYER_NAME, $name ];
			}
		}

		return $errors;
	}

	private function validateDefinition( string $name, mixed $definition ): array {
		if ( !$this->isObject( $definition ) ) {
			return [ [ self::ERROR_NOT_OBJECT, $name ] ];
		}

		$errors = [];

		foreach ( array_keys( $definition ) as $key ) {
			if ( !in_array( $key, self::DEFINITION_KEYS, true ) ) {
				$errors[] = [ self::ERROR_UNKNOWN_LAYER_KEY, $name, (string)$key ];
			}
		}

		$url = $definition['url'] ?? null;
		if ( !is_string( $url ) || !LeafletLayerContract::isValidLayerUrl( $url ) ) {
			$errors[] = [ self::ERROR_INVALID_URL, $name ];
		}

		if ( array_key_exists( 'wms', $definition ) && !is_bool( $definition['wms'] ) ) {
			$errors[] = [ self::ERROR_INVALID_WMS, $name ];
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
			return [ [ self::ERROR_NOT_OBJECT, $name . '.options' ] ];
		}

		$allowed = array_fill_keys( LeafletLayerContract::allowedOptions( $wms ), true );

		$errors = [];

		foreach ( $options as $key => $value ) {
			if ( !isset( $allowed[$key] ) ) {
				$errors[] = [ self::ERROR_UNKNOWN_OPTION, $name, (string)$key ];
			} elseif ( $key === 'attribution' && !is_string( $value ) ) {
				$errors[] = [ self::ERROR_INVALID_ATTRIBUTION, $name ];
			} elseif ( in_array( $key, LeafletLayerContract::URL_OPTIONS, true )
				&& ( !is_string( $value ) || !LeafletLayerContract::isValidLayerUrl( $value ) ) ) {
				$errors[] = [ self::ERROR_INVALID_OPTION_URL, $name, (string)$key ];
			}
		}

		return $errors;
	}

	private function unknownKeyErrors( array $object, array $allowedKeys ): array {
		$errors = [];

		foreach ( array_keys( $object ) as $key ) {
			if ( !in_array( $key, $allowedKeys, true ) ) {
				$errors[] = [ self::ERROR_UNKNOWN_KEY, (string)$key ];
			}
		}

		return $errors;
	}

	private function isObject( mixed $value ): bool {
		return is_array( $value ) && ( $value === [] || !array_is_list( $value ) );
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

	private function isAvailabilityMap( mixed $value ): bool {
		if ( !$this->isObject( $value ) ) {
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
