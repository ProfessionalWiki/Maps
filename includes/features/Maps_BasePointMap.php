<?php

/**
 * Class handling the #display_points rendering.
 *
 * @file
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsBasePointMap extends MapsMapBase {

	/**
	 * @since 0.6.x
	 * 
	 * @var iMappingService
	 */
	protected $service;

	public function __construct( iMappingService $service ) {
		$this->service = $service;
	}

	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param array $params
	 * @param Parser $parser
	 * 
	 * @return html
	 */
	public final function renderMap( array $params, Parser $parser ) {
		$this->handleMarkerData( $params, $parser );

		$mapName = $this->service->getMapId();

		$output = $this->getMapHTML( $params, $parser, $mapName );

		$configVars = Skin::makeVariablesScript( $this->service->getConfigVariables() );

		// MediaWiki 1.17 does not play nice with addScript, so add the vars via the globals hook.
		if ( version_compare( $GLOBALS['wgVersion'], '1.18', '<' ) ) {
			$GLOBALS['egMapsGlobalJSVars'] += $this->service->getConfigVariables();
		}

		global $wgTitle;
		if ( !is_null( $wgTitle ) && $wgTitle->isSpecialPage() ) {
			global $wgOut;
			$this->service->addDependencies( $wgOut );
			$wgOut->addScript( $configVars );
		}
		else {
			$this->service->addDependencies( $parser );
			$parser->getOutput()->addHeadItem( $configVars );			
		}

		return $output;
	}

	/**
	 * Converts the data in the coordinates parameter to JSON-ready objects.
	 * These get stored in the locations parameter, and the coordinates on gets deleted.
	 * 
	 * @since 1.0
	 * 
	 * @param array &$params
	 * @param Parser $parser
	 */
	protected function handleMarkerData( array &$params, Parser $parser ) {
		$parserClone = clone $parser;
		$iconUrl = MapsMapper::getFileUrl( $params['icon'] );
		$params['locations'] = array();

		foreach ( $params['coordinates'] as $location ) {
			if ( $location->isValid() ) {
				$jsonObj = $location->getJSONObject( $params['title'], $params['label'], $iconUrl );

				$jsonObj['title'] = $parserClone->parse( $jsonObj['title'], $parserClone->getTitle(), new ParserOptions() )->getText();
				$jsonObj['text'] = $parserClone->parse( $jsonObj['text'], $parserClone->getTitle(), new ParserOptions() )->getText();
				$jsonObj['inlineLabel'] = strip_tags($parserClone->parse( $jsonObj['inlineLabel'], $parserClone->getTitle(), new ParserOptions() )->getText(),'<a><img>');

				$hasTitleAndtext = $jsonObj['title'] !== '' && $jsonObj['text'] !== '';
				$jsonObj['text'] = ( $hasTitleAndtext ? '<b>' . $jsonObj['title'] . '</b><hr />' : $jsonObj['title'] ) . $jsonObj['text'];
				$jsonObj['title'] = strip_tags( $jsonObj['title'] );

				$params['locations'][] = $jsonObj;				
			}
		}

		unset( $params['coordinates'] );
	}

}
