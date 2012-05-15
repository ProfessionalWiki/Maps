<?php

/**
 * Base class holding common code for MapsBaseMap and MapsBasePointMap.
 * TODO: all this stuff should just be folded into a single parser function.
 *
 * @since 1.1
 *
 * @file
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class MapsMapBase {

	/**
	 * @since 1.1
	 *
	 * @var iMappingService
	 */
	protected $service;

	/**
	 * Constructor.
	 *
	 * @param iMappingService $service
	 */
	public function __construct( iMappingService $service ) {
		$this->service = $service;
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
			wfMsgHtml( 'maps-loading-map' ) .
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

}