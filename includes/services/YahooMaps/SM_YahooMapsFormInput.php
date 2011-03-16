<?php

/**
 * File holding the SMYahooMapsFormInput class.
 *
 * @file SM_YahooMapsFormInput.php
 * @ingroup SMYahooMaps
 * 
 * @author Jeroen De Dauw
 */

/**
 * Class for Yahoo Maps! form inputs.
 * 
 * @ingroup SMYahooMaps
 * 
 * @author Jeroen De Dauw
 */
class SMYahooMapsFormInput extends SMFormInput {
	
	/**
	 * @see SMFormInput::getShowAddressFunction
	 * 
	 * @since 0.6.5
	 */	
	protected function getShowAddressFunction() {
		global $egYahooMapsKey;
		return $egYahooMapsKey == '' ? false : 'showYAddress';	
	}	
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut;
		global $smgScriptPath, $smgStyleVersion;
		
		$this->service->addDependency( Html::linkedScript( "$smgScriptPath/includes/services/YahooMaps/SM_YahooMapsForms.js?$smgStyleVersion" ) );
		$this->service->addDependencies( $wgOut );
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML
	 */
	public function addSpecificMapHTML() {
		return Html::element(
			'div',
			array(
				'id' => $this->service->getMapId( false ),
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);

	}
	
}