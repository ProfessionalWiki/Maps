<?php

/**
 * File containing the registration and initialization code for the Semantic MediaWiki
 * Geographical Coordinates data type, and things it's dependent on.
 * 
 * @file SM_GeoCoords.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

// Registration of the Geographical Coordinate type.
$wgAutoloadClasses['SMGeoCoordsValue'] = dirname( __FILE__ ) . '/SM_GeoCoordsValue.php';

// Registration of the Geographical Coordinate value description class.
$wgAutoloadClasses['SMGeoCoordsValueDescription'] = dirname( __FILE__ ) . '/SM_GeoCoordsValueDescription.php';

// Registration of the Geographical Coordinate are description class.
$wgAutoloadClasses['SMAreaValueDescription'] = dirname( __FILE__ ) . '/SM_AreaValueDescription.php';

// Hook for initializing the Geographical Coordinate type.
$wgHooks['smwInitDatatypes'][] = 'SMGeoCoordsValue::initGeoCoordsType';

// Hook for initializing the field types needed by Geographical Coordinates.
$wgHooks['SMWCustomSQLStoreFieldType'][] = 'SMGeoCoordsValue::initGeoCoordsFieldTypes';

// Hook for defining a table to store geographical coordinates in.
$wgHooks['SMWPropertyTables'][] = 'SMGeoCoordsValue::initGeoCoordsTable';

// Hook for defining the default query printer for queries that ask for geographical coordinates.
$wgHooks['SMWResultFormat'][] = 'SMGeoCoordsValue::addGeoCoordsDefaultFormat';