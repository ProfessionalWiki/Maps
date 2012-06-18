<?php

/**
 * Class for the 'display_map' parser hooks.
 * 
 * @since 0.7
 * 
 * @file Maps_DisplayMap.php
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDisplayMap extends ParserHook {
	/**
	 * No LSB in pre-5.3 PHP *sigh*.
	 * This is to be refactored as soon as php >=5.3 becomes acceptable.
	 */	
	public static function staticInit( Parser &$parser ) {
		$instance = new self;
		return $instance->init( $parser );
	}	
	
	public static function initialize() {
		
	}	
	
	/**
	 * Gets the name of the parser hook.
	 * @see ParserHook::getName
	 * 
	 * @since 0.7
	 * 
	 * @return string
	 */
	protected function getName() {
		return 'display_map';
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * @see ParserHook::getParameterInfo
	 *
	 * TODO: migrate stuff
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
		global $egMapsDefaultServices, $egMapsDefaultTitle, $egMapsDefaultLabel;
		
		$params = MapsMapper::getCommonParameters();

		$params['mappingservice']['default'] = $egMapsDefaultServices['display_map'];
		$params['mappingservice']['manipulations'] = new MapsParamService( 'display_point' );

		$params['zoom']['dependencies'] = array( 'coordinates', 'mappingservice' );
		$params['zoom']['manipulations'] = new MapsParamZoom();
		$params['zoom']['default'] = 7; // FIXME

		$params['coordinates'] = array(
			'name' => 'coordinates',
			'aliases' => array( 'coords', 'location', 'address', 'addresses', 'locations' ),
			'criteria' => new CriterionIsLocation( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ),
			'manipulations' => new MapsParamLocation( $type === ParserHook::TYPE_FUNCTION ? '~' : '|' ),
			'dependencies' => array( 'mappingservice', 'geoservice' ),
			'default' => array(),
			'islist' => true,
			'delimiter' => $type === ParserHook::TYPE_FUNCTION ? ';' : "\n",
			'message' => 'maps-displaypoints-par-coordinates', // TODO
		);

		$manipulation = new MapsParamLocation();
		$manipulation->toJSONObj = true;

		$params['centre'] = array(
			'name' => 'centre',
			'aliases' => array( 'center' ),
			'criteria' => new CriterionIsLocation(),
			'manipulations' => $manipulation,
			'default' => false,
			'manipulatedefault' => false,
			'message' => 'maps-displaypoints-par-centre', // TODO
		);

		$params['title'] = new Parameter(
			'title',
			Parameter::TYPE_STRING,
			$egMapsDefaultTitle
		);
		$params['title']->setMessage( 'maps-displaypoints-par-title' );

		$params['label'] = new Parameter(
			'label',
			Parameter::TYPE_STRING,
			$egMapsDefaultLabel,
			array( 'text' )
		);
		$params['label']->setMessage( 'maps-displaypoints-par-label' );

		$params['icon'] = new Parameter(
			'icon',
			Parameter::TYPE_STRING,
			'', // TODO
			array(),
			array(
				New CriterionNotEmpty()
			)
		);
		$params['icon']->setMessage( 'maps-displaypoints-par-icon' );

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
		return array( 'coordinates' );
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
		$service = MapsMappingServices::getServiceInstance( $parameters['mappingservice'], $this->getName() );
		
		// Get an instance of the class handling the current parser hook and service. 
		$mapClass = $service->getFeatureInstance( $this->getName() );

		return $mapClass->renderMap( $parameters, $this->parser );
	}
	
	/**
	 * Returns the parser function otpions.
	 * @see ParserHook::getFunctionOptions
	 * 
	 * @since 0.7
	 * 
	 * @return array
	 */
	protected function getFunctionOptions() {
		return array(
			'noparse' => true,
			'isHTML' => true
		);
	}

	/**
	 * @see ParserHook::getMessage()
	 * 
	 * @since 1.0
	 */
	public function getMessage() {
		return 'maps-displaymap-description';
	}		
	
}