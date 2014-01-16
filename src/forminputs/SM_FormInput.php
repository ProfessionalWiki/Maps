<?php

use Maps\Elements\Location;

/**
 * Base form input class.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMFormInput {

	/**
	 * @since 1.0
	 * 
	 * @var iMappingService
	 */
	protected $service;		
	
	/**
	 * A character to separate multiple locations with.
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	const SEPARATOR = ';';
	
	/**
	 * Constructor.
	 * 
	 * @since 1.0
	 * 
	 * @param iMappingService $service
	 */
	public function __construct( iMappingService $service ) {
		$this->service = $service;
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * 
	 * @since 1.0
	 * 
	 * @return array
	 */
	protected function getParameterInfo() {
		$params = ParamDefinition::getCleanDefinitions( MapsMapper::getCommonParameters() );

		$this->service->addParameterInfo( $params );

		$params['zoom']['default'] = false;
		$params['zoom']['manipulatedefault'] = false;

		return array_merge( $params, $this->getParameterDefinitions() );
	}

	protected function getParameterDefinitions() {
		global $smgFIFieldSize;

		$params = array();

		$params['fieldsize'] = array(
			'type' => 'integer',
			'default' => $smgFIFieldSize,
			'range' => array( 5, 100 ),
		);

		$params['icon'] = array(
			'default' => '',
		);

		$params['locations'] = array(
			'type' => 'mapslocation',
			'aliases' => array( 'points' ),
			'default' => array(),
			'islist' => true,
			'delimiter' => self::SEPARATOR,
		);

		$params['geocodecontrol'] = array(
			'type' => 'boolean',
			'default' => true,
		);

		// Messages:
		// semanticmaps-par-staticlocations, semanticmaps-par-showtitle,
		// semanticmaps-par-hidenamespace, semanticmaps-par-centre, semanticmaps-par-template,
		// semanticmaps-par-geocodecontrol, semanticmaps-par-activeicon
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'semanticmaps-par-' . $name;
		}

		return $params;
	}
	
	/**
	 * 
	 * 
	 * @since 1.0
	 * 
	 * @param string $coordinates
	 * @param string $input_name
	 * @param boolean $is_mandatory
	 * @param boolean $is_disabled
	 * @param array $field_args
	 * 
	 * @return string
	 */
	public function getInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, array $params ) {
		$parameters = array();
		foreach ( $params as $key => $value ) {
			if ( !is_array( $value ) && !is_object( $value ) && !is_null( $value ) ) {
				$parameters[$key] = $value;
			}
		}

		if ( !is_null( $coordinates ) ) {
			$parameters['locations'] = $coordinates;
		}

		$validator = new Validator( wfMessage( 'maps_' . $this->service->getName() )->text(), false );
		$validator->setParameters( $parameters, $this->getParameterInfo() );
		$validator->validateParameters();
		
		$fatalError  = $validator->hasFatalError();
		
		if ( $fatalError === false ) {
			global $wgParser;
			
			$params = $validator->getParameterValues();
			
			// We can only take care of the zoom defaulting here, 
			// as not all locations are available in whats passed to Validator.
			if ( $params['zoom'] === false && count( $params['locations'] ) <= 1 ) {
				$params['zoom'] = $this->service->getDefaultZoom();
			}			
			
			$mapName = $this->service->getMapId();
			
			$params['inputname'] = $input_name;
			
			$output = $this->getInputHTML( $params, $wgParser, $mapName );

			$this->service->addResourceModules( $this->getResourceModules() );
			
			$configVars = Skin::makeVariablesScript( $this->service->getConfigVariables() );
			
			if ( true /* !is_null( $wgTitle ) && $wgTitle->isSpecialPage() */ ) { // TODO
				global $wgOut;

				$this->service->addDependencies( $wgOut );

				$wgOut->addScript( $configVars );
			}
			else {
				$this->service->addDependencies( $wgParser );
			}

			return $output;
		}
		else {
			return
				'<span class="errorbox">' .
				htmlspecialchars( wfMessage( 'validator-fatal-error', $fatalError->getMessage() )->text() ) .
				'</span>';			
		}			
	}

	/**
	 * @since 2.0
	 *
	 * @param string $coordinates
	 * @param string $input_name
	 * @param boolean $is_mandatory
	 * @param boolean $is_disabled
	 * @param array $params
	 *
	 * @return string
	 */
	public function getEditorInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, array $params ) {
		global $wgOut;

		$wgOut->addHTML( MapsGoogleMaps3::getApiScript(
			'en',
			array( 'libraries' => 'drawing' )
		) );

		$wgOut->addModules( 'mapeditor' );

		$html = Html::element(
			'div',
			array(
				'id' => 'map-polygon',
				'name' => $input_name,
				'cols' => 4,
				'rows' => 2,
			),
			$coordinates
		);

		$editorHtml = new MapEditorHtml( $this->getAttribs() );
		$html = $html . $editorHtml->getEditorHtml();

		return $html;
	}
	
	/**
	 * Returns the HTML to display the map input.
	 * 
	 * @since 1.0
	 * 
	 * @param array $params
	 * @param Parser $parser
	 * @param string $mapName
	 * 
	 * @return string
	 */
	protected function getInputHTML( array $params, Parser $parser, $mapName ) {
		return Html::rawElement(
			'div',
			array(
				'id' => $mapName . '_forminput',
				'style' => 'display: inline',
				'class' => 'sminput sminput-' . $this->service->getName()
			),
			wfMessage( 'semanticmaps-loading-forminput' )->escaped() .
				Html::element(
					'div',
					array( 'style' => 'display:none', 'class' => 'sminputdata' ),
					FormatJson::encode( $this->getJSONObject( $params, $parser ) )
				)
		);
	}
	
	/**
	 * Returns a PHP object to encode to JSON with the map data.
	 *
	 * @since 1.0
	 *
	 * @param array $params
	 * @param Parser $parser
	 * 
	 * @return mixed
	 */	
	protected function getJSONObject( array $params, Parser $parser ) {
		/**
		 * @var Location $location
		 */
		foreach ( $params['locations'] as &$location ) {
			$location = $location->getJSONObject();
		}

		return $params;
	}
	
	/**
	 * @since 1.0
	 * 
	 * @return array of string
	 */
	protected function getResourceModules() {
		return array( 'ext.sm.forminputs' );
	}

	/**
	 * @since 2.1
	 *
	 * @return string
	 */
	protected function getAttribs(){
		return array(
			'id' => 'map-canvas',
			'context' => 'forminput',
			'style' => 'width:600px; height:400px'
		);
	}

}
