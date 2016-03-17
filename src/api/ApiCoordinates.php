<?php

namespace Maps\Api;

use ApiBase;

/**
 * API module for coordinates.
 *
 * @since 3.5.0
 *
 * @ingroup API
 *
 * @licence GNU GPL v2++
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class Coordinates extends ApiBase {

    public function __construct( $main, $action ) {
        parent::__construct( $main, $action );
    }


    public function execute() {
        $params = $this->extractRequestParams();

        $locations = array(
            array(
                "lat" => 40.7127837,
                "lon" => -74.0059413
            ),
            array(
                "lat" => 50.8503396,
                "lon" => 4.3517103
            ),
            array(
                "lat" => 51.5073509,
                "lon" => -0.1277583
            )
        );
        $result = array();
        for ($i = 0; $i < count($locations); $i++) {
            // todo: this is temporary, doesn't work with wrap around etc.
            if ($locations[$i]['lat'] < $params['bbNorth'] &&
                $locations[$i]['lat'] > $params['bbSouth'] &&
                $locations[$i]['lon'] > $params['bbWest'] &&
                $locations[$i]['lon'] < $params['bbEast']) {
                $result['locations'][] = $locations[$i];
            }
        }
        return $this->getResult()->addValue(null, 'results', $result);
    }

    public function getAllowedParams() {
        return array(
            'locations' => array(
                ApiBase::PARAM_TYPE => 'string',
                //ApiBase::PARAM_REQUIRED => true,
                ApiBase::PARAM_ISMULTI => true,
            ),
            'bbSouth' => array(

            ),
            'bbWest' => array(

            ),
            'bbNorth' => array(

            ),
            'bbEast' => array(

            ),
            'service' => array(
                ApiBase::PARAM_TYPE => \Maps\Geocoders::getAvailableGeocoders(),
            ),
            'props' => array(
                ApiBase::PARAM_ISMULTI => true,
                ApiBase::PARAM_TYPE => array( 'lat', 'lon', 'alt' ),
                ApiBase::PARAM_DFLT => 'lat|lon',
            ),
        );
    }

    public function getParamDescription() {
        return array(
            'locations' => 'The locations to geocode',
            'service' => 'The geocoding service to use',
        );
    }

    public function getDescription() {
        return array(
            'API module for geocoding.'
        );
    }

    protected function getExamples() {
        return array(
            'api.php?action=coordinates&locations=new york',
            'api.php?action=coordinates&locations=new york|brussels|london',
            'api.php?action=coordinates&locations=new york&service=geonames',
        );
    }

}
