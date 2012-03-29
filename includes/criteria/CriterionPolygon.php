<?php

/**
 * Parameter criterion stating that the value must be a set of coordinates or an address.
 *
 * @since 0.7
 *
 * @file CriterionLine.php
 * @ingroup Maps
 * @ingroup Criteria
 *
 * @author Kim Eik
 */

class CriterionPolygon extends ItemParameterCriterion
{

    /**
     * Returns true if the parameter value contains atleast 1 colon
     * meaning that there are atleast two enpoints on which to draw a polygon.
     * And if the first and last coordinates are the same, indicating that there is a loop.
     *
     * @param string $value
     * @param Parameter $parameter
     * @param array $parameters
     *
     * @since 0.4
     *
     * @return boolean
     */
    protected function doValidation($value, Parameter $parameter, array $parameters)
    {
        //need atleast two points to create a polygon
        $valid = strpos($value, ':') != false;
        if (!$valid) {
            return $valid;
        }

        //setup geocode deps
        $canGeoCode = MapsGeocoders::canGeocode();
        if ($canGeoCode) {
            $geoService = $parameter->hasDependency('geoservice') ? $parameters['geoservice']->getValue() : '';
            $mappingService = $parameter->hasDependency('mappingservice') ? $parameters['mappingservice']->getValue() : false;
        }

        //strip away line parameters and check for valid locations
        $parts = preg_split('/[:]/', $value);
        foreach ($parts as $part) {
            $toIndex = strpos($part, '|');
            if ($toIndex != false) {
                $part = substr($part, 0, $toIndex);
            }

            if($canGeoCode){
                $valid = MapsGeocoders::isLocation(
                    $part,
                    $geoService,
                    $mappingService
                );
            } else {
                $valid = MapsCoordinateParser::areCoordinates($part);
            }

            if(!$valid){
                break;
            }
        }

        if(MapsCoordinateParser::parseAndFormat($parts[0]) != MapsCoordinateParser::parseAndFormat($parts[sizeof($parts)-1])){
            $valid = false;
        }

        return $valid;
    }

    /**
     * Gets an internationalized error message to construct a ValidationError with
     * when the criteria validation failed. (for non-list values)
     *
     * @param Parameter $parameter
     *
     * @since 0.4
     *
     * @return string
     */
    protected function getItemErrorMessage(Parameter $parameter)
    {
        return wfMsgExt('validation-error-invalid-polyline-param', 'parsemag', $parameter->getOriginalName());
    }
}