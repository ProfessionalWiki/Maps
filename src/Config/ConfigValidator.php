<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * Validates the JSON of the MediaWiki:Maps config page at save time by walking the config schema,
 * producing precise error messages. The schema is strict: unknown groups and keys are rejected, and
 * each value is checked by its type.
 *
 * Errors are returned as message specs: an array whose first element is a message key and whose
 * remaining elements are the message parameters. An empty result means the config is valid.
 */
class ConfigValidator {

	public const ERROR_INVALID_JSON = 'maps-config-error-invalid-json';
	public const ERROR_NOT_OBJECT = 'maps-config-error-not-object';
	public const ERROR_UNKNOWN_KEY = 'maps-config-error-unknown-key';

	public function __construct(
		private ConfigSchema $schema
	) {
	}

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

		return $this->validateGroups( $data );
	}

	private function validateGroups( array $data ): array {
		$errors = [];

		foreach ( $data as $group => $groupData ) {
			$group = (string)$group;

			if ( $this->schema->hasGroup( $group ) ) {
				$errors = array_merge( $errors, $this->validateGroup( $group, $groupData ) );
			} else {
				$errors[] = [ self::ERROR_UNKNOWN_KEY, $group ];
			}
		}

		return $errors;
	}

	private function validateGroup( string $group, mixed $groupData ): array {
		if ( !$this->isObject( $groupData ) ) {
			return [ [ self::ERROR_NOT_OBJECT, $group ] ];
		}

		$errors = [];

		foreach ( $groupData as $key => $value ) {
			$key = (string)$key;
			$setting = $this->schema->getSetting( $group, $key );

			if ( $setting === null ) {
				$errors[] = [ self::ERROR_UNKNOWN_KEY, $group . '.' . $key ];
			} else {
				$errors = array_merge( $errors, $setting->type->validate( $value, $group . '.' . $key ) );
			}
		}

		return $errors;
	}

	private function isObject( mixed $value ): bool {
		return is_array( $value ) && ( $value === [] || !array_is_list( $value ) );
	}

}
