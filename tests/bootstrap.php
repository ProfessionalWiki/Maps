<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

$pwd = exec( 'pwd' );
chdir( __DIR__ . '/..' );
passthru( 'composer update' );
chdir( $pwd );

require_once( __DIR__ . '/evilMediaWikiBootstrap.php' );

require_once( __DIR__ . '/../vendor/autoload.php' );

foreach ( $GLOBALS['wgExtensionFunctions'] as $extensionFunction ) {
	call_user_func( $extensionFunction );
}

$GLOBALS['wtfIsThisShit'] = $GLOBALS['wgHooks'];