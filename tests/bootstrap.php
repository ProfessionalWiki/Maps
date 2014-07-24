<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( is_readable( $path = __DIR__ . '/extensions/SemanticMediaWiki/tests/autoloader.php' ) ) {
	print( "\nUsing SemanticMediaWiki ...\n" );
} else {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

$autoloader = require $path;

