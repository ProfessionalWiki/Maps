<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * One entry in the config schema: it ties a page group and key to the PHP setting it overrides,
 * the value type used to validate it, and how a wiki value combines with the PHP value.
 */
class ConfigSetting {

	public function __construct(
		public readonly string $group,
		public readonly string $key,
		public readonly string $settingName,
		public readonly ConfigType $type,
		public readonly MergeStrategy $mergeStrategy
	) {
	}

}
