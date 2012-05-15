<?php

/**
 * Class handling the #display_map rendering.
 *
 * @file
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsBaseMap extends MapsMapBase {
	
	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @since 1.0
	 *
	 * @param array $params
	 * @param Parser $parser
	 * 
	 * @return html
	 */
	public final function renderMap( array $params, Parser $parser ) {
		$this->setCentre( $params );
		
		if ( $params['zoom'] === false ) {
			$params['zoom'] = $this->service->getDefaultZoom();
		}
		
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
	 * Translates the coordinates field to the centre field and makes sure it's set to it's default when invalid. 
	 * 
	 * @since 1.0
	 * 
	 * @param array &$params
	 */
	protected function setCentre( array &$params ) {
		// If it's false, the coordinate was invalid, or geocoding failed. Either way, the default's should be used.
		if ( $params['coordinates'] === false ) {
			global $egMapsDefaultMapCentre;
		
			$centre = MapsGeocoders::attemptToGeocode( $egMapsDefaultMapCentre, $params['geoservice'], $this->service->getName() );
			
			if ( $centre === false ) {
				throw new MWException( 'Failed to parse the default centre for the map. Please check the value of $egMapsDefaultMapCentre.' );
			}
			else {
				$params['centre'] = $centre;
			}
		}
		else {
			$params['centre'] = $params['coordinates'];
		}
		
		unset( $params['coordinates'] );
	}
	
}