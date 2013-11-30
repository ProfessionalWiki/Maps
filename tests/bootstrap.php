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

// Note: You do need to include Maps.php from your LocalSettings.php file.