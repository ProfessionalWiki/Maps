<?php

/**
 * Class for the 'display_map_ajax' parser hooks.
 *
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class SMDisplayAjaxMap extends MapsDisplayMap {

    /**
     * Gets the name of the parser hook.
     * @see ParserHook::getName
     *
     * @since 0.7
     *
     * @return string
     */
    protected function getName() {
        return 'display_ajax_map';
    }

    /**
     * Returns an array containing the parameter info.
     * @see ParserHook::getParameterInfo
     *
     * @since 0.7
     *
     * @return array
     */
    protected function getParameterInfo( $type ) {
        $params = MapsMapper::getCommonParameters();

        $params['mappingservice']['feature'] = 'display_ajax_map';

        $params['coordinates'] = array(
            //'type' => 'mapslocation',
            //'aliases' => array( 'coords', 'location', 'address', 'addresses', 'locations', 'points' ),
            'dependencies' => array( 'mappingservice', 'geoservice' ),
            'default' => '',
            'islist' => true,
            'delimiter' => $type === ParserHook::TYPE_FUNCTION ? ';' : "\n",
            'message' => 'maps-displaymap-par-coordinates',
        );

        $params['ajax'] = array(
            'type' => 'boolean',
            'default' => true,
            'message' => 'maps-displaymap-par-ajax',
        );

        $params['coordinatesproperty'] = array(
            'default' => 'Has coordinates',
            'message' => 'maps-displaymap-par-coordinatesproperty',
        );

        $params = array_merge( $params, self::getCommonMapParams() );

        return $params;
    }

    /**
     * Renders and returns the output.
     * @see ParserHook::render
     *
     * @since 0.7
     *
     * @param array $parameters
     *
     * @return string
     */
    public function render( array $parameters ) {
        // Get the instance of the service class.
        $service = MapsMappingServices::getServiceInstance( $parameters['mappingservice'] );
        $mapName = $service->getMapId();

        //$fullParams = $this->validator->getParameters();

        $configVars = Skin::makeVariablesScript( $service->getConfigVariables() );
        $service->addDependencies( $this->parser );
        $this->parser->getOutput()->addHeadItem( $configVars );

        /*if ( array_key_exists( 'zoom', $fullParams ) && $fullParams['zoom']->wasSetToDefault() && count( $parameters['coordinates'] ) > 1 ) {
            $parameters['zoom'] = false;
        }

        $this->parser->addTrackingCategory( 'maps-tracking-category' );*/

        return Html::rawElement(
            'div',
            array(
                'id' => $mapName,
                'style' => "width: {$parameters['width']}; height: {$parameters['height']}; background-color: #cccccc; overflow: hidden;",
                'class' => 'maps-map maps-' . $service->getName()
            ),
            wfMessage( 'maps-loading-map' )->inContentLanguage()->escaped() .
            Html::element(
                'div',
                array( 'style' => 'display:none', 'class' => 'mapdata' ),
                FormatJson::encode( $parameters )
            )
        );
    }

}
