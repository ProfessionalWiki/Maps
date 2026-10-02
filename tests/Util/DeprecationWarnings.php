<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use MediaWiki\Debug\MWDebug;

class DeprecationWarnings {

	/**
	 * @return string[]
	 */
	public static function during( callable $action ): array {
		// MediaWiki emits each deprecation warning once per process, so an earlier test may already have emitted it.
		MWDebug::clearLog();

		$warnings = [];

		set_error_handler(
			static function ( int $level, string $message ) use ( &$warnings ): bool {
				$warnings[] = $message;
				return true;
			},
			E_USER_DEPRECATED
		);

		try {
			$action();
		} finally {
			restore_error_handler();
		}

		return $warnings;
	}

}
