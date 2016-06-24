<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( -1 );
ini_set( 'display_errors', 1 );

if ( !is_readable( $path = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The Semantic MediaWiki test autoloader is not available' );
}

if ( !class_exists( 'SemanticMaps' ) || ( $version = SemanticMaps::getVersion() ) === null ) {
	die( "\nSemantic Maps is not available, please check your Composer or LocalSettings.\n" );
}

print sprintf( "\n%-20s%s\n", "Semantic Maps: ", $version );

require $path;
