<?php

/**
 * File defining the settings for the Semantic Maps extension
 * More info can be found at http://www.mediawiki.org/wiki/Extension:Semantic Maps#Settings
 *
 *                          NOTICE:
 * Changing one of these settings can be done by copieng or cutting it, 
 * and placing it in LocalSettings.php, AFTER the inclusion of Semantic Maps.
 *
 * @file SM_Settings.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}




# Map features configuration
# (named) Array of String. This array contains the available features for Maps.
# The array element name contains an abbriviation, used for code references,
# and in the service data arrays, the value is the human readible version for displaying purpouses.
$egMapsAvailableFeatures['qp'] = 'Query Printer';
$egMapsAvailableFeatures['fi'] = 'Form input';


