<?php

/**
 * This file holds the general information for the OpenLayers service.
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'Not an entry point.' );
}

$moduleTemplate = [
    'localBasePath' => __DIR__,
    'remoteExtPath' => 'SemanticMaps/src/services/OpenLayers',
    'group' => 'ext.semanticmaps',
];

$GLOBALS['wgResourceModules']['ext.sm.fi.openlayersajax'] = $moduleTemplate + [
        'dependencies' => [
            'ext.maps.openlayers'
        ],
        'scripts' => [
            'ext.sm.openlayersajax.js'
        ]
    ];

unset( $moduleTemplate );

$GLOBALS['wgHooks']['MappingServiceLoad'][] = 'smfInitOpenLayers';

function smfInitOpenLayers() {
    /* @var MapsMappingService $openlayers */
    $openlayers = MapsMappingServices::getServiceInstance( 'openlayers' );
    $openlayers->addResourceModules(array( 'ext.sm.fi.openlayersajax' ));

    return true;
}
