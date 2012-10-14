<?php

/**
 * Base form input class.
 *
 * @file SM_FormInput.php
 * @ingroup SemanticMaps
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
	 * @var char
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
		global $smgFIMulti, $smgFIFieldSize;

		$params = ParamDefinition::getCleanDefinitions( MapsMapper::getCommonParameters() );

		$this->service->addParameterInfo( $params );
		
		$params['zoom']->setDefault( false, false );		
		
		$params['multi'] = new Parameter( 'multi', Parameter::TYPE_BOOLEAN );
		$params['multi']->setDefault( $smgFIMulti, false );
		
		$params['fieldsize'] = new Parameter( 'fieldsize', Parameter::TYPE_INTEGER );
		$params['fieldsize']->setDefault( $smgFIFieldSize, false );
		$params['fieldsize']->addCriteria( new CriterionInRange( 5, 100 ) );

		$params['icon'] = new Parameter( 'icon' );
		$params['icon']->setDefault( '' );
		$params['icon']->addCriteria( New CriterionNotEmpty() );

		$manipulation = new MapsParamLocation();
		$manipulation->toJSONObj = true;

		$params['locations'] = array(
			'aliases' => array( 'points' ),
			'criteria' => new CriterionIsLocation(),
			'manipulations' => $manipulation,
			'default' => array(),
			'islist' => true,
			'delimiter' => self::SEPARATOR,
			'message' => 'semanticmaps-par-locations', // TODO
		);
		
		$params['geocodecontrol'] = new Parameter( 'geocodecontrol', Parameter::TYPE_BOOLEAN );
		$params['geocodecontrol']->setDefault( true, false );
		$params['geocodecontrol']->setMessage( 'semanticmaps-par-geocodecontrol' );
		
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
			
			// MediaWiki 1.17 does not play nice with addScript, so add the vars via the globals hook.
			if ( version_compare( $GLOBALS['wgVersion'], '1.18', '<' ) ) {
				$GLOBALS['egMapsGlobalJSVars'] += $this->service->getConfigVariables();
			}
			
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
	 * @since 2.0 alspha
	 * 
	 * @param string $coordinates
	 * @param string $input_name
	 * @param boolean $is_mandatory
	 * @param boolean $is_disabled
	 * @param array $field_args
	 * 
	 * @return string
	 */
	public function getEditorInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, array $params ) {
		global $wgOut;
		$parameters = array();
		$wgOut->addHtml( MapsGoogleMaps3::getApiScript(
			'en',
			array( 'libraries' => 'drawing' )
		) );

		$wgOut->addModules( 'mapeditor' );

		$output = <<<EOT
		<div >
			<textarea id="map-polygon" name="$input_name" cols="4" rows="2"></textarea>
		</div>
<div id="map-canvas" context="forminput" style= "width:600px; height:400px">
	</div>
<div style="display: none;">
    <pre id="code-output" title="%1\$s"></pre>
    <div id="code-input-container" title="%2\$s" >
        <p>%3\$s</p>
        <textarea id="code-input" rows="15"></textarea>
    </div>
    <div id="marker-form" class="mapeditor-dialog" title="%4\$s">
        <div class="link-title-switcher">
            <input type="radio" name="switch" value="text" /> %5\$s
            <input type="radio" name="switch" value="link" /> %6\$s
        </div>
        <form class="mapeditor-dialog-form">
            <fieldset>
                <label for="m-title">%7\$s</label>
                <input type="text" name="title" id="m-title" class="text ui-widget-content ui-corner-all"/>
                <label for="m-text">%8\$s</label>
                <input type="text" name="text" id="m-text" class="text ui-widget-content ui-corner-all"/>
                <label for="m-link">%9\$s</label>
                <input type="text" name="link" id="m-link" class="text ui-widget-content ui-corner-all"/>
                <label for="m-icon">%10\$s</label>
                <input type="text" name="icon" id="m-icon" class="text ui-widget-content ui-corner-all"/>
                <label for="m-group">%11\$s</label>
                <input type="text" name="group" id="m-group" class="text ui-widget-content ui-corner-all"/>
                <label for="m-inlinelabel">%12\$s</label>
                <input type="text" name="inlinelabel" id="m-inlinelabel" class="text ui-widget-content ui-corner-all"/>
                <label for="m-visitedicon">%23\$s</label>
                <input type="text" name="visitedicon" id="m-visitedicon" class="text ui-widget-content ui-corner-all"/>
            </fieldset>
        </form>
    </div>

    <div id="strokable-form" class="mapeditor-dialog" title="%4\$s">
        <div class="link-title-switcher">
            <input type="radio" name="switch" value="text" /> %5\$s
            <input type="radio" name="switch" value="link" /> %6\$s
        </div>
        <form class="mapeditor-dialog-form">
            <fieldset>
                <label for="s-title">%7\$s</label>
                <input type="text" name="title" id="s-title" class="text ui-widget-content ui-corner-all"/>
                <label for="s-text">%8\$s</label>
                <input type="text" name="text" id="s-text" value="" class="text ui-widget-content ui-corner-all"/>
                <label for="s-link">%9\$s</label>
                <input type="text" name="link" id="s-link" class="text ui-widget-content ui-corner-all"/>
                <label for="s-strokecolor">%13\$s</label>
                <input type="text" name="strokeColor" id="s-strokecolor" class="text ui-widget-content ui-corner-all"/>
                <label for="s-strokeopacity">%14\$s</label>
                <input type="hidden" name="strokeOpacity" id="s-strokeopacity" class="text ui-widget-content ui-corner-all"/>
                <label for="s-strokeweight">%15\$s</label>
                <input type="text" name="strokeWeight" id="s-strokeweight" class="text ui-widget-content ui-corner-all"/>
            </fieldset>
        </form>
    </div>

    <div id="fillable-form" class="mapeditor-dialog" title="%4\$s">
        <div class="link-title-switcher">
            <input type="radio" name="switch" value="text" /> %5\$s
            <input type="radio" name="switch" value="link" /> %6\$s
        </div>
        <form class="mapeditor-dialog-form">
            <fieldset>
                <label for="f-title">%7\$s</label>
                <input type="text" name="title" id="f-title" class="text ui-widget-content ui-corner-all"/>
                <label for="f-text">%8\$s</label>
                <input type="text" name="text" id="f-text" value="" class="text ui-widget-content ui-corner-all"/>
                <label for="f-link">%9\$s</label>
                <input type="text" name="link" id="f-link" class="text ui-widget-content ui-corner-all"/>
                <label for="f-strokecolor">%13\$s</label>
                <input type="text" name="strokeColor" id="f-strokecolor" class="text ui-widget-content ui-corner-all"/>
                <label for="f-strokeopacity">%14\$s</label>
                <input type="hidden" name="strokeOpacity" id="f-strokeopacity" class="text ui-widget-content ui-corner-all"/>
                <label for="f-strokeweight">%15\$s</label>
                <input type="text" name="strokeWeight" id="f-strokeweight" class="text ui-widget-content ui-corner-all"/>
                <label for="f-fillcolor">%16\$s</label>
                <input type="text" name="fillColor" id="f-fillcolor" class="text ui-widget-content ui-corner-all"/>
                <label for="f-fillopacity">%17\$s</label>
                <input type="hidden" name="fillOpacity" id="f-fillopacity" class="text ui-widget-content ui-corner-all"/>
            </fieldset>
        </form>
    </div>

    <div id="polygon-form" class="mapeditor-dialog" title="%4\$s">
        <div class="link-title-switcher">
            <input type="radio" name="switch" value="text" /> %5\$s
            <input type="radio" name="switch" value="link" /> %6\$s
        </div>
        <form class="mapeditor-dialog-form">
            <fieldset>
                <label for="p-title">%7\$s</label>
                <input type="text" name="title" id="p-title" class="text ui-widget-content ui-corner-all"/>
                <label for="p-text">%8\$s</label>
                <input type="text" name="text" id="p-text" value="" class="text ui-widget-content ui-corner-all"/>
                <label for="p-link">%9\$s</label>
                <input type="text" name="link" id="p-link" class="text ui-widget-content ui-corner-all"/>
                <label for="p-strokecolor">%13\$s</label>
                <input type="text" name="strokeColor" id="p-strokecolor" class="text ui-widget-content ui-corner-all"/>
                <label for="p-strokeopacity">%14\$s</label>
                <input type="hidden" name="strokeOpacity" id="p-strokeopacity" class="text ui-widget-content ui-corner-all"/>
                <label for="p-strokeweight">%15\$s</label>
                <input type="text" name="strokeWeight" id="p-strokeweight" class="text ui-widget-content ui-corner-all"/>
                <label for="p-fillcolor">%16\$s</label>
                <input type="text" name="fillColor" id="p-fillcolor" class="text ui-widget-content ui-corner-all"/>
                <label for="p-fillopacity">%17\$s</label>
                <input type="hidden" name="fillOpacity" id="p-fillopacity" class="text ui-widget-content ui-corner-all"/>
                <label for="p-showonhover">%18\$s</label>
                <input type="checkbox" name="showOnHover" id="p-showonhover" class="text ui-widget-content ui-corner-all"/>
            </fieldset>
        </form>
    </div>
    <div id="map-parameter-form" class="mapeditor-dialog" title="%19\$s">
        <form class="mapeditor-dialog-form">
            <div>
                <select name="key">
                    <option value="">%20\$s</option>
                </select>
            </div>
        </form>
    </div>
    <div id="imageoverlay-form" title="%22\$s">
        <div class="link-title-switcher">
            <input type="radio" name="switch" value="text" /> %5\$s
            <input type="radio" name="switch" value="link" /> %6\$s
        </div>
        <form class="mapeditor-dialog-form">
            <fieldset>
                <label for="i-title">%7\$s</label>
                <input type="text" name="title" id="i-title" class="text ui-widget-content ui-corner-all"/>
                <label for="i-text">%8\$s</label>
                <input type="text" name="text" id="i-text" class="text ui-widget-content ui-corner-all"/>
                <label for="i-link">%9\$s</label>
                <input type="text" name="link" id="i-link" class="text ui-widget-content ui-corner-all"/>
                <label for="i-image">%21\$s</label>
                <input type="text" name="image" id="i-image" class="text ui-widget-content ui-corner-all"/>
            </fieldset>
        </form>
    </div>
</div>
EOT;
		$html = sprintf(
			$output,
			wfMessage( 'mapeditor-code-title' ),
			wfMessage( 'mapeditor-import-title' ),
			wfMessage( 'mapeditor-import-note' ),
			wfMessage( 'mapeditor-form-title' ),
			wfMessage( 'mapeditor-link-title-switcher-popup-text' ),
			wfMessage( 'mapeditor-link-title-switcher-link-text' ),
			wfMessage( 'mapeditor-form-field-title' ),
			wfMessage( 'mapeditor-form-field-text' ),
			wfMessage( 'mapeditor-form-field-link' ),
			wfMessage( 'mapeditor-form-field-icon' ),
			wfMessage( 'mapeditor-form-field-group' ),
			wfMessage( 'mapeditor-form-field-inlinelabel' ),
			wfMessage( 'mapeditor-form-field-strokecolor' ),
			wfMessage( 'mapeditor-form-field-strokeopacity' ),
			wfMessage( 'mapeditor-form-field-strokeweight' ),
			wfMessage( 'mapeditor-form-field-fillcolor' ),
			wfMessage( 'mapeditor-form-field-fillopcaity' ),
			wfMessage( 'mapeditor-form-field-showonhover' ),
			wfMessage( 'mapeditor-mapparam-title' ),
			wfMessage( 'mapeditor-mapparam-defoption' ),
			wfMessage( 'mapeditor-form-field-image' ),
			wfMessage( 'mapeditor-imageoverlay-title' ),
			wfMessage( 'mapeditor-form-field-visitedicon' )
		);
		return $html;
	}
}
