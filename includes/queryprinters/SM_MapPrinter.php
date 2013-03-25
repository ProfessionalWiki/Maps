<?php

/**
 * Query printer for maps. Is invoked via SMMapper.
 * Can be overridden per service to have custom output.
 *
 * @file SM_MapPrinter.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMMapPrinter extends SMW\ResultPrinter {
	
	/**
	 * @since 0.6
	 * 
	 * @var iMappingService
	 */
	protected $service;	
	
	/**
	 * @since 1.0
	 * 
	 * @var string|boolean false
	 */
	protected $fatalErrorMsg = false;
	
	/**
	 * Constructor.
	 * 
	 * @param $format String
	 * @param $inline
	 */
	public function __construct( $format, $inline = true ) {
		$this->service = MapsMappingServices::getValidServiceInstance( $format, 'qp' );
		
		parent::__construct( $format, $inline );
	}

	/**
	 * Returns an array containing the parameter info.
	 * 
	 * @since 1.0
	 * 
	 * @return array
	 */
	protected function getParameterInfo() {
		global $smgQPForceShow, $smgQPShowTitle, $smgQPTemplate, $smgQPHideNamespace;
		
		$params = ParamDefinition::getCleanDefinitions( MapsMapper::getCommonParameters() );

		$this->service->addParameterInfo( $params );

		$params['staticlocations'] = array(
			'type' => 'mapslocation',
			'aliases' => array( 'locations', 'points' ),
			'default' => array(),
			'islist' => true,
			'delimiter' => ';',
			'message' => 'semanticmaps-par-staticlocations',
		);

		$params['forceshow'] = array(
			'type' => 'boolean',
			'aliases' => 'force show',
			'default' => $smgQPForceShow,
		);

		$params['showtitle'] = array(
			'type' => 'boolean',
			'aliases' => 'show title',
			'default' => $smgQPShowTitle,
		);

		$params['hidenamespace'] = array(
			'type' => 'boolean',
			'aliases' => 'hide namespace',
			'default' => $smgQPHideNamespace,
		);

		$params['template'] = array(
			'default' => $smgQPTemplate,
		);

		// Give grep a chance to find the usages:
		// semanticmaps-par-staticlocations, semanticmaps-par-forceshow, semanticmaps-par-showtitle,
		// semanticmaps-par-hidenamespace, semanticmaps-par-centre, semanticmaps-par-template,
		// semanticmaps-par-geocodecontrol
		foreach ( $params as $name => &$data ) {
			if ( is_array( $data ) && !array_key_exists( 'message', $data ) ) {
				$data['message'] = 'semanticmaps-par-' . $name;
			}
		}

		$params = array_merge( $params, MapsDisplayMap::getCommonMapParams() );
		
		return $params;
	}
	
	/**
	 * Builds up and returns the HTML for the map, with the queried coordinate data on it.
	 *
	 * @param SMWQueryResult $res
	 * @param $outputmode
	 * 
	 * @return array or string
	 */
	public final function getResultText( SMWQueryResult $res, $outputmode ) {
		if ( $this->fatalErrorMsg !== false ) {
			return $this->fatalErrorMsg;
		}

		/**
		 * @var Parser $wgParser
		 */
		global $wgParser;

		$params = $this->params;

		$queryHandler = new SMQueryHandler( $res, $outputmode );
		$queryHandler->setLinkStyle($params['link']);
		$queryHandler->setHeaderStyle($params['headers']);
		$queryHandler->setShowSubject( $params['showtitle'] );
		$queryHandler->setTemplate( $params['template'] );
		$queryHandler->setHideNamespace( $params['hidenamespace'] );

		$this->handleMarkerData( $params, $queryHandler );
		$locationAmount = count( $params['locations'] );

		if ( $params['forceshow'] || $locationAmount > 0 ) {
			// We can only take care of the zoom defaulting here,
			// as not all locations are available in whats passed to Validator.
			if ( $this->fullParams['zoom']->wasSetToDefault() && $locationAmount > 1 ) {
				$params['zoom'] = false;
			}

			$mapName = $this->service->getMapId();

			SMWOutputs::requireHeadItem(
				$mapName,
				$this->service->getDependencyHtml() .
				$configVars = Skin::makeVariablesScript( $this->service->getConfigVariables() )
			);

			foreach ( $this->service->getResourceModules() as $resourceModule ) {
				SMWOutputs::requireResource( $resourceModule );
			}

			if ( array_key_exists( 'source', $params ) ) {
				unset( $params['source'] );
			}

			return $this->getMapHTML( $params, $wgParser, $mapName );
		}
		else {
			return $params['default'];
		}
	}

	/**
	 * Returns the HTML to display the map.
	 *
	 * @since 1.1
	 *
	 * @param array $params
	 * @param Parser $parser
	 * @param string $mapName
	 *
	 * @return string
	 */
	protected function getMapHTML( array $params, Parser $parser, $mapName ) {
		return Html::rawElement(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: {$params['width']}; height: {$params['height']}; background-color: #cccccc; overflow: hidden;",
				'class' => 'maps-map maps-' . $this->service->getName()
			),
			wfMessage( 'maps-loading-map' )->inContentLanguage()->escaped() .
				Html::element(
					'div',
					array( 'style' => 'display:none', 'class' => 'mapdata' ),
					FormatJson::encode( $this->getJSONObject( $params, $parser ) )
				)
		);
	}

	/**
	 * Returns a PHP object to encode to JSON with the map data.
	 *
	 * @since 1.1
	 *
	 * @param array $params
	 * @param Parser $parser
	 *
	 * @return mixed
	 */
	protected function getJSONObject( array $params, Parser $parser ) {
		return $params;
	}
	
	/**
	 * Converts the data in the coordinates parameter to JSON-ready objects.
	 * These get stored in the locations parameter, and the coordinates on gets deleted.
	 * 
	 * @since 1.0
	 * 
	 * @param array &$params
	 * @param $queryHandler
	 */
	protected function handleMarkerData( array &$params, $queryHandler ) {
		if ( is_object( $params['centre'] ) ) {
			$params['centre'] = $params['centre']->getJSONObject();
		}

		/**
		 * @var Parser $wgParser
		 */
		global $wgParser;

		$parser = version_compare( $GLOBALS['wgVersion'], '1.18', '<' ) ? $wgParser : clone $wgParser;
		
		$iconUrl = MapsMapper::getFileUrl( $params['icon'] );
		$visitedIconUrl = MapsMapper::getFileUrl( $params['visitedicon'] );
		$params['locations'] = array();

		foreach ( $params['staticlocations'] as $location ) {
			$jsonObj = $location->getJSONObject( $params['title'], $params['label'], $iconUrl, '', '', $visitedIconUrl );

			$jsonObj['title'] = $parser->parse( $jsonObj['title'], $parser->getTitle(), new ParserOptions() )->getText();
			$jsonObj['text'] = $parser->parse( $jsonObj['text'], $parser->getTitle(), new ParserOptions() )->getText();

			$hasTitleAndtext = $jsonObj['title'] !== '' && $jsonObj['text'] !== '';
			$jsonObj['text'] = ( $hasTitleAndtext ? '<b>' . $jsonObj['title'] . '</b><hr />' : $jsonObj['title'] ) . $jsonObj['text'];
			$jsonObj['title'] = strip_tags( $jsonObj['title'] );

			$params['locations'][] = $jsonObj;
		}

		unset( $params['staticlocations'] );

		$queryShapes = $queryHandler->getShapes();
		
		foreach ( $queryShapes['locations'] as $location ) {
			$jsonObj = $location->getJSONObject( $params['title'], $params['label'], $iconUrl, '', '', $visitedIconUrl );

			$jsonObj['title'] = strip_tags( $jsonObj['title'] );

			$params['locations'][] = $jsonObj;
		}

		foreach ( $queryShapes['lines'] as $line ) {
			$jsonObj = $line->getJSONObject( $params['title'], $params['label'] );
			$params['lines'][] = $jsonObj;				
		}
		foreach ( $queryShapes['polygons'] as $polygon ) {
			$jsonObj = $polygon->getJSONObject( $params['title'], $params['label'] );
			$params['polygons'][] = $jsonObj;				
		}


	}

	/**
	 * Returns the internationalized name of the mapping service.
	 * 
	 * @return string
	 */
	public final function getName() {
		return wfMessage( 'maps_' . $this->service->getName() )->text();
	}
	
	/**
	 * Returns a list of parameter information, for usage by Special:Ask and others.
	 * 
	 * @return array
	 */
    public function getParameters() {
        $params = parent::getParameters();
        $paramInfo = $this->getParameterInfo();
        
        // Do not display this as an option, as the format already determines it
        // TODO: this can probably be done cleaner with some changes in Maps
        unset( $paramInfo['mappingservice'] );
        
        $params = array_merge( $params, $paramInfo );

		return $params;
    }
}
