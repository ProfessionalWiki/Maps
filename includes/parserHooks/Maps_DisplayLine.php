<?php

/**
 * Class for the 'display_line' parser hooks.
 *
 * @since 0.7
 *
 * @file Maps_DisplayLine.php
 * @ingroup Maps
 *
 * @author Kim Eik
 */
class MapsDisplayLine extends MapsDisplayPoint {

	/**
	 * No LSB in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */
	public static function staticInit( Parser &$parser ) {
		$instance = new self;
		return $instance->init( $parser );
	}


	/**
	 * Gets the name of the parser hook.
	 *
	 * @since 0.4
	 *
	 * @return string or array of string
	 */
	protected function getName() {
		return 'display_line';
	}

	/**
	 * @param array $parameters
	 * @return mixed
	 */
	public function render( array $parameters ) {
		// Get the instance of the service class.
		$service = MapsMappingServices::getServiceInstance( $parameters['mappingservice'] );

		// Get an instance of the class handling the current parser hook and service.
		$mapClass = $service->getFeatureInstance( $this->getName() );

		return $mapClass->renderMap( $parameters , $this->parser );
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
		global $egMapsDefaultServices;

		$params = parent::getParameterInfo( $type );

		$params['mappingservice']->setDefault( $egMapsDefaultServices[$this->getName()] );
		$params['mappingservice']->addManipulations( new MapsParamService( $this->getName() ) );

		$params['lines'] = new ListParameter( 'lines' , ';' );
		$params['lines']->setDefault( array() );
		$params['lines']->addCriteria( new CriterionLine( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ) );
		$params['lines']->addManipulations( new MapsParamLine( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ) );

		$params['polygons'] = new ListParameter( 'polygons' , ';' );
		$params['polygons']->setDefault( array() );
		$params['polygons']->addCriteria( new CriterionPolygon( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ) );
		$params['polygons']->addManipulations( new MapsParamPolygon( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ) );

		$params['circles'] = new ListParameter( 'circles' , ';' );
		$params['circles']->setDefault( array() );
		$params['circles']->addManipulations( new MapsParamCircle( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ) );

		$params['rectangles'] = new ListParameter( 'rectangles' , ';' );
		$params['rectangles']->setDefault( array() );
		$params['rectangles']->addManipulations( new MapsParamRectangle( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ) );

		$params['copycoords'] = new Parameter(
			'copycoords' ,
			Parameter::TYPE_BOOLEAN
		);
		$params['copycoords']->setDefault( false );
		$params['copycoords']->setDoManipulationOfDefault( false );


		$params['markercluster'] = new Parameter(
			'markercluster' ,
			Parameter::TYPE_BOOLEAN
		);
		$params['markercluster']->setDefault( false );
		$params['markercluster']->setDoManipulationOfDefault( false );

		$params['searchmarkers'] = new Parameter(
			'searchmarkers' ,
			Parameter::TYPE_STRING
		);
		$params['searchmarkers']->setDefault( '' );
		$params['searchmarkers']->addCriteria( new CriterionSearchMarkers() );
		$params['searchmarkers']->setDoManipulationOfDefault( false );

		$params['static'] = new Parameter(
			'static' ,
			Parameter::TYPE_BOOLEAN
		);
		$params['static']->setDefault( false );
		$params['static']->setDoManipulationOfDefault( false );

		$params['maxzoom'] = new Parameter(
			'maxzoom',
			Parameter::TYPE_INTEGER
		);
		$params['maxzoom']->setDefault( false );
		$params['maxzoom']->setDoManipulationOfDefault( false );
		$params['maxzoom']->addDependencies( 'minzoom' );

		$params['minzoom'] = new Parameter(
			'minzoom',
			Parameter::TYPE_INTEGER
		);
		$params['minzoom']->setDefault( false );
		$params['minzoom']->setDoManipulationOfDefault( false );

		return $params;
	}

	/**
	 * Returns the list of default parameters.
	 * @see ParserHook::getDefaultParameters
	 *
	 * @since 0.7
	 *
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return array( 'coordinates' , 'lines' , 'polygons' , 'circles' , 'rectangles' );
	}
}