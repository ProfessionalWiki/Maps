<?php

namespace SMW\MediaWiki\Api;

use ApiBase;
use SMWQueryProcessor;
use SMW\Query\PrintRequest;
use SMWPropertyValue;

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
class Coordinates extends Query {

    public function __construct( $main, $action ) {
        parent::__construct( $main, $action );
    }


    public function execute() {
        $parameterFormatter = new ApiRequestParameterFormatter( $this->extractRequestParams() );
        $outputFormat = 'json';

        $conditions = $parameterFormatter->getAskArgsApiParameter( 'conditions' );
        $conditions .= '[[Has coordinates::+]]';

        $printouts = $parameterFormatter->getAskArgsApiParameter( 'printouts' );
        $printouts[] = new PrintRequest(
            PrintRequest::PRINT_PROP,
            'Has coordinates',
            SMWPropertyValue::makeUserProperty( 'Has coordinates' )
        );

        $parameters = $parameterFormatter->getAskArgsApiParameter( 'parameters' );

        $queryResult = $this->getQueryResult( $this->getQuery(
            $conditions,
            $printouts,
            $parameters
        ) );

        if ( $this->getMain()->getPrinter() instanceof \ApiFormatXml ) {
            $outputFormat = 'xml';
        }

        // todo filter by bounding box

        $this->addQueryResult( $queryResult, $outputFormat );

        /*$params = $this->extractRequestParams();

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
        return $this->getResult()->addValue(null, 'results', $result);*/
    }

    public function getAllowedParams() {
        return array(
            'conditions' => array(
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_ISMULTI => true,
                ApiBase::PARAM_REQUIRED => true,
            ),
            'printouts' => array(
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_DFLT => '',
                ApiBase::PARAM_ISMULTI => true,
            ),
            'parameters' => array(
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_DFLT => '',
                ApiBase::PARAM_ISMULTI => true,
            ),
            'bbSouth' => array(
                ApiBase::PARAM_REQUIRED => true
            ),
            'bbWest' => array(
                ApiBase::PARAM_REQUIRED => true
            ),
            'bbNorth' => array(
                ApiBase::PARAM_REQUIRED => true
            ),
            'bbEast' => array(
                ApiBase::PARAM_REQUIRED => true
            )
        );
    }

    public function getParamDescription() {
        return array(
            'conditions' => 'The query conditions, i.e. the requirements for a subject to be included',
            'printouts'  => 'The query printouts, i.e. the properties to show per subject',
            'parameters' => 'The query parameters, i.e. all non-condition and non-printout arguments',
            'bbSouth' => 'Bounding box South',
            'bbWest' => 'Bounding box West',
            'bbNorth' => 'Bounding box North',
            'bbEast' => 'Bounding box East'
        );
    }

    public function getDescription() {
        return array(
            'API module for geocoding.'
        );
    }

    protected function getExamples() {
        return array(
            'api.php?action=coordinates&conditions=Category:Locations&printouts=&parameters=&bbSouth=-100&bbWest=-100&bbNorth=100&bbEast=100',
        );
    }

}
