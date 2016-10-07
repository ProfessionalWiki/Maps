<?php

use Maps\Elements\Location;
use ParamProcessor\ParamDefinition;
use ParamProcessor\ProcessingError;
use ParamProcessor\Processor;

/**
 * Base form input class.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMFormInput {

	/**
	 * @var iMappingService
	 */
	private $service;
	
	/**
	 * A character to separate multiple locations with.
	 * 
	 * @var string
	 */
	const SEPARATOR = ';';
	
	/**
	 * Constructor.
	 * 
	 * @param iMappingService $service
	 */
	public function __construct( iMappingService $service ) {
		$this->service = $service;
	}
	
	/**
	 * Returns an array containing the parameter info.
	 * 
	 * @return array
	 */
	private function getParameterInfo() {
		$params = ParamDefinition::getCleanDefinitions( MapsMapper::getCommonParameters() );

		$this->service->addParameterInfo( $params );

		$params['zoom']['default'] = false;
		$params['zoom']['manipulatedefault'] = false;

		return array_merge( $params, $this->getParameterDefinitions() );
	}

	private function getParameterDefinitions() {
		global $smgFIFieldSize;

		$params = [];

		$params['fieldsize'] = [
			'type' => 'integer',
			'default' => $smgFIFieldSize,
			'range' => [ 5, 100 ],
		];

		$params['icon'] = [
			'default' => '',
		];

		$params['locations'] = [
			'type' => 'mapslocation',
			'aliases' => [ 'points' ],
			'default' => [],
			'islist' => true,
			'delimiter' => self::SEPARATOR,
		];

		$params['geocodecontrol'] = [
			'type' => 'boolean',
			'default' => true,
		];

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
	 * @param string $coordinates
	 * @param string $inputName
	 * @param boolean $isMandatory
	 * @param boolean $isDisabled
	 * @param array $params
	 * 
	 * @return string
	 */
	public function getInputOutput( $coordinates, $inputName, $isMandatory, $isDisabled, array $params ) {
		$parameters = [];
		foreach ( $params as $key => $value ) {
			if ( !is_array( $value ) && !is_object( $value ) && !is_null( $value ) ) {
				$parameters[$key] = $value;
			}
		}

		if ( !is_null( $coordinates ) ) {
			$parameters['locations'] = $coordinates;
		}

		$validator = Processor::newDefault();
		$validator->setParameters( $parameters, $this->getParameterInfo() );
		$processingResult = $validator->processParameters();

		if ( $processingResult->hasFatal() ) {
			return $this->getFatalOutput( $validator->getErrors() );
		}
		else {
			return $this->getMapOutput( $validator->getParameterValues(), $inputName );
		}			
	}

	private function getMapOutput( array $params, $inputName ) {
		global $wgParser;

		if ( is_object( $params['centre'] ) ) {
			$params['centre'] = $params['centre']->getJSONObject();
		}

		// We can only take care of the zoom defaulting here,
		// as not all locations are available in whats passed to Validator.
		if ( $params['zoom'] === false && count( $params['locations'] ) <= 1 ) {
			$params['zoom'] = $this->service->getDefaultZoom();
		}

		$mapName = $this->service->getMapId();

		$params['inputname'] = $inputName;

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

	/**
	 * @param ProcessingError[] $errors
	 *
	 * @return string
	 */
	private function getFatalOutput( array $errors ) {
		$html = '';

		foreach ( $errors as $error ) {
			if ( $error->isFatal() ) {
				$html .= $this->errorToHtml( $error );
			}
		}

		return $html;
	}

	private function errorToHtml( ProcessingError $error ) {
		return
			'<span class="errorbox">' .
			htmlspecialchars( wfMessage( 'validator-fatal-error', $error->getMessage() )->text() ) .
			'</span>';
	}

	/**
	 * @param string $coordinates
	 * @param string $input_name
	 * @param boolean $isMandatory
	 * @param boolean $isDisabled
	 * @param array $params
	 *
	 * @return string
	 */
	public function getEditorInputOutput( $coordinates, $input_name, $isMandatory, $isDisabled, array $params ) {
		global $wgOut;

		$wgOut->addHTML( MapsGoogleMaps3::getApiScript(
			'en',
			[ 'libraries' => 'drawing' ]
		) );

		$wgOut->addModules( 'mapeditor' );

		$html = Html::element(
			'div',
			[
				'id' => 'map-polygon',
				'name' => $input_name,
				'cols' => 4,
				'rows' => 2,
			],
			$coordinates
		);

		$editorHtml = new MapEditorHtml( $this->getAttribs() );

		return $html . $editorHtml->getEditorHTML();
	}
	
	/**
	 * Returns the HTML to display the map input.
	 * 
	 * @param array $params
	 * @param Parser $parser
	 * @param string $mapName
	 * 
	 * @return string
	 */
	private function getInputHTML( array $params, Parser $parser, $mapName ) {
		return Html::rawElement(
			'div',
			[
				'id' => $mapName . '_forminput',
				'style' => 'display: inline',
				'class' => 'sminput sminput-' . $this->service->getName()
			],
			wfMessage( 'semanticmaps-loading-forminput' )->escaped() .
				Html::element(
					'div',
					[ 'style' => 'display:none', 'class' => 'sminputdata' ],
					FormatJson::encode( $this->getJSONObject( $params, $parser ) )
				)
		);
	}
	
	/**
	 * Returns a PHP object to encode to JSON with the map data.
	 *
	 * @param array $params
	 * @param Parser $parser
	 * 
	 * @return mixed
	 */
	private function getJSONObject( array $params, Parser $parser ) {
		/**
		 * @var Location $location
		 */
		foreach ( $params['locations'] as &$location ) {
			$location = $location->getJSONObject();
		}

		return $params;
	}
	
	/**
	 * @return array of string
	 */
	protected function getResourceModules() {
		return [ 'ext.sm.forminputs' ];
	}

	/**
	 * @return string
	 */
	private function getAttribs() {
		return [
			'id' => 'map-canvas',
			'context' => 'forminput',
			'style' => 'width:600px; height:400px'
		];
	}

}
