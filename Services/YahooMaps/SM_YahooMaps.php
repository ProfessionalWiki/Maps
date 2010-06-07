<?php

/**
 * This groupe contains all Yahoo! Maps related files of the Semantic Maps extension.
 * 
 * @defgroup SMYahooMaps Yahoo! Maps
 * @ingroup SemanticMaps
 */

/**
 * This file holds the general information for the Yahoo! Maps service.
 *
 * @file SM_YahooMaps.php
 * @ingroup SMYahooMaps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['SMYahooMapsQP'] = dirname( __FILE__ ) . '/SM_YahooMapsQP.php';
// TODO: the if should not be needed, but when omitted, a fatal error occurs cause the class that's extended by this one is not found.
if ( defined( 'SF_VERSION' ) ) $wgAutoloadClasses['SMYahooMapsFormInput'] = dirname( __FILE__ ) . '/SM_YahooMapsFormInput.php';

$egMapsServices['yahoomaps']['features']['qp'] = 'SMYahooMapsQP';
$egMapsServices['yahoomaps']['features']['fi'] = 'SMYahooMapsFormInput';