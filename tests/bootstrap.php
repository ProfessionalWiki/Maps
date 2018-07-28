<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( defined( 'MEDIAWIKI' ) ) {
	// If testing against an older version of MediaWiki, define
	// an empty trait to avoid fatal errors.
	if ( !trait_exists( PHPUnit4And6Compat::class ) ) {
		trait PHPUnit4And6Compat {
			public function expectException( string $exception ) {
				$this->setExpectedException( $exception );
			}
		}
	}

	return;
}

if ( !trait_exists( PHPUnit4And6Compat::class ) ) {
	trait PHPUnit4And6Compat {
	}
}

error_reporting( -1 );
ini_set( 'display_errors', 1 );

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

require __DIR__ . '/../vendor/autoload.php';