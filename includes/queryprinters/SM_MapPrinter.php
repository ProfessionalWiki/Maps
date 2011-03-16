<?php

/**
 * Query printer for maps. Is invoked via SMMapper.
 * Can be overriden per service to have custom output.
 *
 * @file SM_MapPrinter.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMMapPrinter extends SMWResultPrinter {
	
	/**
	 * @since 0.6
	 * 
	 * @var iMappingService
	 */
	protected $service;	
	
	/**
	 * @since 0.8
	 * 
	 * @var false or string
	 */
	protected $fatalErrorMsg = false;
	
	/**
	 * @since 0.8
	 * 
	 * @var array
	 */
	protected $parameters;
	
	/**
	 * Constructor.
	 * 
	 * @param $format String
	 * @param $inline
	 * @param $service iMappingService
	 */
	public function __construct( $format, $inline, iMappingService $service ) {
		$this->service = $service;
	}

	/**
	 * (non-PHPdoc)
	 * @see SMWResultPrinter::readParameters()
	 */
	protected function readParameters( /* array */ $params, $outputmode ) {
		parent::readParameters( $params, $outputmode );

		$validator = new Validator( $this->getName(), false );
		$validator->setParameters( $params, $this->getParameterInfo() );
		$validator->validateParameters();
		
		$fatalError  = $validator->hasFatalError();
		
		if ( $fatalError === false ) {
			$this->parameters = $validator->getParameterValues();
		}
		else {
			$this->fatalErrorMsg =
				'<span class="errorbox">' .
				htmlspecialchars( wfMsgExt( 'validator-fatal-error', 'parsemag', $fatalError->getMessage() ) ) . 
				'</span>';			
		}	
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * 
	 * @since 0.8
	 * 
	 * @return array
	 */
	protected function getParameterInfo() {
		global $egMapsDefaultLabel, $egMapsDefaultTitle;
		global $smgQPForceShow, $smgQPShowTitle, $smgQPTemplate;
		
		$params = MapsMapper::getCommonParameters();
		$this->service->addParameterInfo( $params );		
		
		$params['zoom']->setDefault( false );		
		$params['zoom']->setDoManipulationOfDefault( false );		
		
		$params['staticlocations'] = new ListParameter( 'staticlocations', ';' );
		$params['staticlocations']->addAliases( 'locations' );
		$params['staticlocations']->addCriteria( new CriterionIsLocation( '~' ) );
		$params['staticlocations']->addManipulations( new MapsParamLocation( '~' ) );		
		$params['staticlocations']->setDefault( array() );
		
		$params['centre'] = new Parameter( 'centre' );
		$params['centre']->setDefault( false );
		$params['centre']->addAliases( 'center' );
		$params['centre']->addCriteria( new CriterionIsLocation() );
		$params['centre']->setDoManipulationOfDefault( false );
		$manipulation = new MapsParamLocation();
		$manipulation->toJSONObj = true;
		$params['centre']->addManipulations( $manipulation );	
		
		$params['icon'] = new Parameter(
			'icon',
			Parameter::TYPE_STRING,
			'',
			array(),
			array(
				New CriterionNotEmpty()
			)
		);
		
		$params['forceshow'] = new Parameter(
			'forceshow',
			Parameter::TYPE_BOOLEAN,
			$smgQPForceShow,
			array( 'force show' )
		);

		$params['showtitle'] = new Parameter(
			'showtitle',
			Parameter::TYPE_BOOLEAN,
			$smgQPShowTitle,
			array( 'show title' )
		);
		
		$params['template'] = new Parameter(
			'template',
			Parameter::TYPE_STRING,
			$smgQPTemplate,
			array(),
			array(
				New CriterionNotEmpty()
			)
		);
		$params['template']->setDoManipulationOfDefault( false );
		
		$params['title'] = new Parameter(
			'title',
			Parameter::TYPE_STRING,
			$egMapsDefaultTitle
		);
		
		$params['label'] = new Parameter(
			'label',
			Parameter::TYPE_STRING,
			$egMapsDefaultLabel,
			array( 'text' )
		);
		
		return $params;
	}	
	
	/**
	 * Builds up and returns the HTML for the map, with the queried coordinate data on it.
	 *
	 * @param SMWQueryResult $res
	 * @param $outputmode
	 * 
	 * @return array
	 */
	public final function getResultText( /* SMWQueryResult */ $res, $outputmode ) {
		if ( $this->fatalErrorMsg === false ) {
			global $wgParser;
			
			$params = $this->parameters;
			
			$queryHandler = new SMQueryHandler( $res, $outputmode );
			$queryHandler->setShowSubject( $params['showtitle'] );
			$queryHandler->setTemplate( $params['template'] );
			
			$this->handleMarkerData( $params, $queryHandler->getLocations() );
			$locationAmount = count( $params['locations'] );
			
			if ( $params['forceshow'] || $locationAmount > 0 ) {
				// We can only take care of the zoom defaulting here, 
				// as not all locations are available in whats passed to Validator.
				if ( $params['zoom'] === false && $locationAmount <= 1 ) {
					$params['zoom'] = $this->service->getDefaultZoom();
				}
				
				$mapName = $this->service->getMapId();
				
				SMWOutputs::requireHeadItem( $mapName, $this->service->getDependencyHtml() );
				foreach ( $this->service->getResourceModules() as $resourceModule ) {
					SMWOutputs::requireResource( $resourceModule );
				}
				
				return array(
					$this->getMapHTML( $params, $wgParser, $mapName ) . $this->getJSON( $params, $wgParser, $mapName ),
					'noparse' => true, 
					'isHTML' => true
				);				
			}
			else {
				return '';
			}
		}
		else {
			return $this->fatalErrorMsg;
		}
	}
	
	/**
	 * Returns the HTML to display the map.
	 * 
	 * @since 0.8
	 * 
	 * @param array $params
	 * @param Parser $parser
	 * @param string $mapName
	 * 
	 * @return string
	 */
	protected function getMapHTML( array $params, Parser $parser, $mapName ) {
		return Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: {$params['width']}; height: {$params['height']}; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
	}	
	
	/**
	 * Returns the JSON with the maps data.
	 *
	 * @since 0.8
	 *
	 * @param array $params
	 * @param Parser $parser
	 * @param string $mapName
	 * 
	 * @return string
	 */	
	protected function getJSON( array $params, Parser $parser, $mapName ) {
		$object = $this->getJSONObject( $params, $parser );
		
		if ( $object === false ) {
			return '';
		}
		
		return Html::inlineScript(
			MapsMapper::getBaseMapJSON( $this->service->getName() )
			. "maps.{$this->service->getName()}.{$mapName}=" . json_encode( $object ) . ';'
		);
	}
	
	/**
	 * Returns a PHP object to encode to JSON with the map data.
	 *
	 * @since 0.8
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
	 * @since 0.8
	 * 
	 * @param array &$params
	 * @param array $queryLocations
	 */
	protected function handleMarkerData( array &$params, array $queryLocations ) {
		global $wgTitle;

		$parser = new Parser();			
		$iconUrl = MapsMapper::getFileUrl( $params['icon'] );
		$params['locations'] = array();

		foreach ( array_merge( $params['staticlocations'], $queryLocations ) as $location ) {
			if ( $location->isValid() ) {
				$jsonObj = $location->getJSONObject( $params['title'], $params['label'], $iconUrl );
				
				$jsonObj['title'] = strip_tags( $jsonObj['title'] );
				
				$params['locations'][] = $jsonObj;				
			}
		}
		
		unset( $params['staticlocations'] );
	}	
	
	/**
	 * Reads the parameters and gets the query printers output.
	 * 
	 * @param SMWQueryResult $results
	 * @param array $params
	 * @param $outputmode
	 * 
	 * @return array
	 */
	public final function getResult( /* SMWQueryResult */ $results, /* array */ $params, $outputmode ) {
		// Skip checks, results with 0 entries are normal.
		$this->readParameters( $params, $outputmode );
		
		return $this->getResultText( $results, SMW_OUTPUT_HTML );
	}

	/**
	 * Returns the internationalized name of the mapping service.
	 * 
	 * @return string
	 */
	public final function getName() {
		return wfMsg( 'maps_' . $this->service->getName() );
	}
	
	/**
	 * Returns a list of parameter information, for usage by Special:Ask and others.
	 * 
	 * @return array
	 */
    public function getParameters() {
        $params = parent::getParameters();
        
		// Now go through the descriptions, and convert them from Validator- to SMW-style.
		foreach ( $this->getParameterInfo() as $paramDesc ) {
			$param = array(
				'name' => $paramDesc->getName(),
				'type' => $this->getMappedParamType( $paramDesc->getType() ),
				'description' => $paramDesc->getDescription() ? $paramDesc->getDescription() : '',
				'default' => $paramDesc->isRequired() ? '' : $paramDesc->getDefault()
			);
			
	        foreach ( $paramDesc->getCriteria() as $criterion ) {
	    		if ( $criterion instanceof CriterionInArray ) {
	    			$param['values'] = $criterion->getAllowedValues();
	    			$param['type'] = $paramDesc->isList() ? 'enum-list' : 'enumeration';
	    			break;
	    		}
	    	} 

	    	$params[] = $param;
		}

        return $params;
    }
    
    /**
     * Takes in an element of the Parameter::TYPE_ enum and turns it into an SMW type (string) indicator.
     * 
     * @since 0.8
     * 
     * @param Parameter::TYPE_ $type
     * 
     * @return string
     */
    protected function getMappedParamType( $type ) {
    	static $typeMap = array(
    		Parameter::TYPE_STRING => 'string',
    		Parameter::TYPE_BOOLEAN => 'boolean',
    		Parameter::TYPE_CHAR => 'int',
    		Parameter::TYPE_FLOAT => 'int',
    		Parameter::TYPE_INTEGER => 'int',
    		Parameter::TYPE_NUMBER => 'int',
    	);
    	
    	return $typeMap[$type];
    }
	
}
