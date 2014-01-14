<?php

namespace Maps\Elements;

/**
 * Class that holds metadata on WMS overlay layers on map
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Mathias Lidal < mathiaslidal@gmail.com >
 */
class WmsOverlay extends BaseElement {

	/**
	 * @since 3.0
	 * @var String Base url to WMS server
	 */
	protected $wmsServerUrl;

	/**
	 * @since 3.0
	 * @var String WMS Layer name
	 */
	protected $wmsLayerName;

	/**
	 * @since 3.0
	 * @var String WMS Stype name (default value: 'default')
	 */
	protected $wmsStyleName;

	/**
	 * @since 3.0
	 *
	 * @param string $wmsServerUrl
	 * @param string $wmsLayerName
	 */
	public function __construct( $wmsServerUrl, $wmsLayerName, $wmsStyleName="default" ) {
		parent::__construct();
		$this->setWmsServerUrl( $wmsServerUrl );
		$this->setWmsLayerName( $wmsLayerName );
		$this->setWmsStyleName( $wmsStyleName );
	}

	/**
	 * @since 3.0
	 *
	 * @param String $wmsLayerName
	 */
	public function setWmsLayerName( $wmsLayerName ) {
		$this->wmsLayerName = $wmsLayerName;
	}

	/**
	 * @since 3.0
	 *
	 * @return String
	 */
	public function getWmsLayerName() {
		return $this->wmsLayerName;
	}

	/**
	 * @since 3.0
	 *
	 * @param String $wmsServerUrl
	 */
	public function setWmsServerUrl( $wmsServerUrl ) {
		$this->wmsServerUrl = $wmsServerUrl;
	}

	/**
	 * @since 3.0
	 *
	 * @return String
	 */
	public function getWmsServerUrl() {
		return $this->wmsServerUrl;
	}

	/**
	 * @since 3.0
	 *
	 * @param String $wmsStyleName
	 */
	public function setWmsStyleName( $wmsStyleName ) {
		$this->wmsStyleName = $wmsStyleName;
	}

	/**
	 * @return String
	 */
	public function getWmsStyleName() {
		return $this->wmsStyleName;
	}
	/**
	 * @since 3.0
	 *
	 * @return array
	 */
	public function getJSONObject ( $defText = "", $defTitle = "" ) {
		$parentArray = parent::getJSONObject( $defText , $defTitle );

		$array = array (
			'wmsServerUrl' => $this->getWmsServerUrl() ,
			'wmsLayerName' => $this->getWmsLayerName() ,
			'wmsStyleName' => $this->getWmsStyleName()
		);
		return array_merge( $parentArray, $array );
	}

}
