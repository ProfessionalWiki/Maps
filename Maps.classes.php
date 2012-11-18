<?php

$classes = array();

$classes['MapsHooks'] = __DIR__ . '/Maps.hooks.php';

$classes['Maps\Element'] 				= __DIR__ . '/includes/Element.php';
$classes['MapsMapper'] 					= __DIR__ . '/includes/Maps_Mapper.php';
$classes['MapsDistanceParser'] 			= __DIR__ . '/includes/Maps_DistanceParser.php';
$classes['MapsGeoFunctions'] 			= __DIR__ . '/includes/Maps_GeoFunctions.php';
$classes['MapsGeocoders'] 				= __DIR__ . '/includes/Maps_Geocoders.php';
$classes['MapsGeocoder'] 				= __DIR__ . '/includes/Maps_Geocoder.php';
$classes['MapsKMLFormatter'] 			= __DIR__ . '/includes/Maps_KMLFormatter.php';
$classes['MapsLayer'] 					= __DIR__ . '/includes/Maps_Layer.php';
$classes['MapsLayerPage'] 				= __DIR__ . '/includes/Maps_LayerPage.php';
$classes['MapsLayers'] 					= __DIR__ . '/includes/Maps_Layers.php';
$classes['MapsLocation'] 				= __DIR__ . '/includes/Maps_Location.php';
$classes['iMappingService'] 			= __DIR__ . '/includes/iMappingService.php';
$classes['MapsMappingServices'] 		= __DIR__ . '/includes/Maps_MappingServices.php';
$classes['MapsMappingService'] 			= __DIR__ . '/includes/Maps_MappingService.php';
$classes['MapsWmsOverlay'] 				= __DIR__ . '/includes/Maps_WmsOverlay.php';
$classes['MapsBaseFillableElement'] 	= __DIR__ . '/includes/Maps_BaseFillableElement.php';
$classes['MapsBaseStrokableElement'] 	= __DIR__ . '/includes/Maps_BaseStrokableElement.php';
$classes['MapsDisplayMapRenderer'] 		= __DIR__ . '/includes/Maps_DisplayMapRenderer.php';

$classes['Maps\BaseElement']			= __DIR__ . '/includes/elements/BaseElement.php';
$classes['Maps\Circle'] 				= __DIR__ . '/includes/elements/Circle.php';
$classes['Maps\ImageOverlay'] 			= __DIR__ . '/includes/ImageOverlay.php';
$classes['Maps\Line'] 					= __DIR__ . '/includes/elements/Line.php';
$classes['Maps\Polygon'] 				= __DIR__ . '/includes/elements/Polygon.php';
$classes['Maps\Rectangle'] 				= __DIR__ . '/includes/elements/Rectangle.php';


$classes['ApiGeocode'] 					= __DIR__ . '/includes/api/ApiGeocode.php';

$classes['Maps\ServiceParam'] 			= __DIR__ . '/includes/ServiceParam.php';

$classes['MapsCoordinates'] 			= __DIR__ . '/includes/parserhooks/Maps_Coordinates.php';
$classes['MapsDisplayMap'] 				= __DIR__ . '/includes/parserhooks/Maps_DisplayMap.php';
$classes['MapsDistance'] 				= __DIR__ . '/includes/parserhooks/Maps_Distance.php';
$classes['MapsFinddestination'] 		= __DIR__ . '/includes/parserhooks/Maps_Finddestination.php';
$classes['MapsGeocode'] 				= __DIR__ . '/includes/parserhooks/Maps_Geocode.php';
$classes['MapsGeodistance'] 			= __DIR__ . '/includes/parserhooks/Maps_Geodistance.php';
$classes['MapsMapsDoc'] 				= __DIR__ . '/includes/parserhooks/Maps_MapsDoc.php';

$classes['Maps\DistanceParser'] 		= __DIR__ . '/includes/parsers/DistanceParser.php';
$classes['Maps\LineParser'] 			= __DIR__ . '/includes/parsers/LineParser.php';
$classes['Maps\LocationParser'] 		= __DIR__ . '/includes/parsers/LocationParser.php';
$classes['Maps\PolygonParser'] 			= __DIR__ . '/includes/parsers/PolygonParser.php';

$classes['iBubbleMapElement'] 			= __DIR__ . '/includes/properties/iBubbleMapElement.php';
$classes['iFillableMapElement'] 		= __DIR__ . '/includes/properties/iFillableMapElement.php';
$classes['iHoverableMapElement'] 		= __DIR__ . '/includes/properties/iHoverableMapElement.php';
$classes['iLinkableMapElement'] 		= __DIR__ . '/includes/properties/iLinkableMapElement.php';
$classes['iStrokableMapElement'] 		= __DIR__ . '/includes/properties/iStrokableMapElement.php';

$classes['MapsGeonamesGeocoder'] 		= __DIR__ . '/includes/geocoders/Maps_GeonamesGeocoder.php';
$classes['MapsGoogleGeocoder'] 			= __DIR__ . '/includes/geocoders/Maps_GoogleGeocoder.php';

$classes['SpecialMapEditor'] 			= __DIR__ . '/includes/specials/SpecialMapEditor.php';

$classes['Maps\Test\ParserHookTest'] 	= __DIR__ . '/tests/phpunit/parserhooks/ParserHookTest.php';

return $classes;