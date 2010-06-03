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
if ( class_exists( 'SMFormInput' ) ) $wgAutoloadClasses['SMYahooMapsFormInput'] = dirname( __FILE__ ) . '/SM_YahooMapsFormInput.php';

$egMapsServices['yahoomaps']['features']['qp'] = 'SMYahooMapsQP';
$egMapsServices['yahoomaps']['features']['fi'] = 'SMYahooMapsFormInput';