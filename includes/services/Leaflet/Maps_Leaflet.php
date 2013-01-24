<?php

/**
 * Class holding information and functionality specific to Leaflet.
 * This information and features can be used by any mapping feature.
 *
 * @file Maps_Leaflet.php
 * @ingroup Leaflet
 *
 * @licence GNU GPL v2+
 * @author Pavel Astakhov < pastakhov@yandex.ru >
 */
class MapsLeaflet extends MapsMappingService {

    /**
     * Constructor
     */
    function __construct( $serviceName ) {
        parent::__construct(
            $serviceName,
            array( 'leafletmaps', 'leaflet' )
        );
    }

    /**
     * @see MapsMappingService::addParameterInfo
     *
     * @since leaflet
     */
    public function addParameterInfo( array &$params ) {

        $params['zoom'] = array(
            'type' => 'integer',
            'range' => array( 0, 20 ),
            'default' => false,
            'message' => 'maps-leaflet-par-zoom', //TODO
            );

        $params['defzoom'] = array(
            'type' => 'integer',
            'range' => array( 0, 20 ),
            'default' => self::getDefaultZoom(),
            'message' => 'maps-leaflet-par-defzoom', //TODO
            );

        $params['resizable'] = array(
            'type' => 'boolean',
            'default' => $GLOBALS['egMapsResizableByDefault'],
            'message' => 'maps-leaflet-par-resizable', //TODO
            );
    }

    /**
     * @see iMappingService::getDefaultZoom
     *
     * @since 0.6.5
     */
    public function getDefaultZoom() {
        return $GLOBALS['egMapsLeafletZoom'];
    }

    /**
     * @see MapsMappingService::getMapId
     *
     * @since 0.6.5
     */
    public function getMapId( $increment = true ) {
        static $mapsOnThisPage = 0;

        if ( $increment ) {
            $mapsOnThisPage++;
        }

        return 'map_leaflet_' . $mapsOnThisPage;
    }

    /**
     * @see MapsMappingService::getResourceModules
     *
     * @since 1.0
     *
     * @return array of string
     */
    public function getResourceModules() {
        return array_merge(
            parent::getResourceModules(),
            array( 'ext.maps.leaflet' )
        );
    }
}
