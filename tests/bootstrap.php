<?php

if ( defined( 'MEDIAWIKI' ) ) {
	return;
}

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( -1 );
ini_set( 'display_errors', 1 );

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

require __DIR__ . '/../vendor/composer/ClassLoader.php';

call_user_func( function() {
	$loader = new \Composer\Autoload\ClassLoader();

	foreach ( require __DIR__ . '/../vendor/composer/autoload_namespaces.php' as $namespace => $path ) {
		$loader->set( $namespace, $path );
	}

	foreach ( require __DIR__ . '/../vendor/composer/autoload_psr4.php' as $namespace => $path ) {
		$loader->setPsr4( $namespace, $path );
	}

	$classMap = require __DIR__ . '/../vendor/composer/autoload_classmap.php';

	if ( $classMap ) {
		$loader->addClassMap( $classMap );
	}

	$loader->register( true );
} );

