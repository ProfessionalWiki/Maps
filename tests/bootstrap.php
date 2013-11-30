<?php

/**
 * PHPUnit test bootstrap file for the Maps extension.
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

echo exec( 'composer update' ) . "\n";

require_once( __DIR__ . '/evilMediaWikiBootstrap.php' );

require_once( __DIR__ . '/../vendor/autoload.php' );

foreach ( $GLOBALS['wgExtensionFunctions'] as $extensionFunction ) {
	call_user_func( $extensionFunction );
}