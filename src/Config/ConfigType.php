<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * A value type in the config schema. It validates a single decoded JSON value from the
 * MediaWiki:Maps config page, both at save time (to produce precise editor errors) and at read time
 * (to guard against values that bypassed save validation). One type maps to one kind of setting,
 * such as a boolean, an enum or a dimension.
 */
interface ConfigType {

	/**
	 * Validates a decoded value from the config page. The location is the dotted path of the value,
	 * such as "general.mapWidth", used in error messages.
	 *
	 * @return array[] List of message specs, each [ messageKey, ...params ]. Empty when valid.
	 */
	public function validate( mixed $value, string $location ): array;

	/**
	 * Describes the accepted value shape for the on-page configuration reference. The description is
	 * about the value type, not the individual setting: its semantics live in the external
	 * documentation, reached via the setting name shown alongside it.
	 *
	 * @return array A single message spec, [ messageKey, ...params ].
	 */
	public function describe(): array;

}
