<?php

use DataValues\Geo\Parsers\GeoCoordinateParser;
use Maps\ServiceParam;
use Maps\LocationParser;
use Maps\LineParser;
use Maps\CircleParser;
use Maps\RectangleParser;
use Maps\PolygonParser;
use Maps\DistanceParser;
use Maps\WmsOverlayParser;
use Maps\ImageOverlayParser;

/**
 * Static class for hooks handled by the Maps extension.
 *
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsHooks {

 	public static function onExtensionCallback() {
		if ( defined( 'Maps_COORDS_FLOAT' ) ) {
			// Do not initialize more than once.
			return 1;
		}

		// The different coordinate notations.
		define( 'Maps_COORDS_FLOAT' , 'float' );
		define( 'Maps_COORDS_DMS' , 'dms' );
		define( 'Maps_COORDS_DM' , 'dm' );
		define( 'Maps_COORDS_DD' , 'dd' );

		require_once __DIR__ . '/Maps_Settings.php';

		define( 'Maps_VERSION' , '4.2.1' );
		define( 'SM_VERSION', Maps_VERSION );

		// Only initialize the extension when all dependencies are present.
		if ( !defined( 'Validator_VERSION' ) ) {
			throw new Exception( 'You need to have Validator installed in order to use Maps' );
		}

		$GLOBALS['egMapsStyleVersion'] = $GLOBALS['wgStyleVersion'] . '-' . Maps_VERSION;
	}

	public static function onExtensionFunction() {
		if ( $GLOBALS['egMapsDisableExtension'] ) {
			return true;
		}
		if ( defined( 'Maps_VERSION' ) ) {
			// Do not initialize more than once.
			return true;
		}

		// Only initialize the extension when all dependencies are present.
		if ( !defined( 'Validator_VERSION' ) ) {
			throw new Exception( 'You need to have Validator installed in order to use Maps' );
		}

		define( 'Maps_VERSION' , '4.2.1' );
		define( 'SM_VERSION', Maps_VERSION );

		if ( $GLOBALS['egMapsGMaps3Language'] === '' ) {
			$GLOBALS['egMapsGMaps3Language'] = $GLOBALS['wgLang'];
		}

		MapsMappingServices::registerService( 'googlemaps3', MapsGoogleMaps3::class );

		$googleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
		$googleMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );

		MapsMappingServices::registerService(
			'openlayers',
			MapsOpenLayers::class,
			[ 'display_map' => MapsDisplayMapRenderer::class ]
		);

		MapsMappingServices::registerService( 'leaflet', MapsLeaflet::class );
		$leafletMaps = MapsMappingServices::getServiceInstance( 'leaflet' );
		$leafletMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );

		if ( in_array( 'googlemaps3', $GLOBALS['egMapsAvailableServices'] ) ) {
			$GLOBALS['wgSpecialPages']['MapEditor'] = 'SpecialMapEditor';
			$GLOBALS['wgSpecialPageGroups']['MapEditor'] = 'maps';
		}

		$GLOBALS['egMapsStyleVersion'] = $GLOBALS['wgStyleVersion'] . '-' . Maps_VERSION;

		// Users that can geocode. By default the same as those that can edit.
		foreach ( $GLOBALS['wgGroupPermissions'] as $group => $rights ) {
			if ( array_key_exists( 'edit' , $rights ) ) {
				$GLOBALS['wgGroupPermissions'][$group]['geocode'] = $GLOBALS['wgGroupPermissions'][$group]['edit'];
			}
		}

		$GLOBALS['wgParamDefinitions']['coordinate'] = [
			'string-parser' => GeoCoordinateParser::class,
		];

		$GLOBALS['wgParamDefinitions']['mappingservice'] = [
			'definition'=> ServiceParam::class,
		];

		$GLOBALS['wgParamDefinitions']['mapslocation'] = [
			'string-parser' => LocationParser::class,
		];

		$GLOBALS['wgParamDefinitions']['mapsline'] = [
			'string-parser' => LineParser::class,
		];

		$GLOBALS['wgParamDefinitions']['mapscircle'] = [
			'string-parser' => CircleParser::class,
		];

		$GLOBALS['wgParamDefinitions']['mapsrectangle'] = [
			'string-parser' => RectangleParser::class,
		];

		$GLOBALS['wgParamDefinitions']['mapspolygon'] = [
			'string-parser' => PolygonParser::class,
		];

		$GLOBALS['wgParamDefinitions']['distance'] = [
			'string-parser' => DistanceParser::class,
		];

		$GLOBALS['wgParamDefinitions']['wmsoverlay'] = [
			'string-parser' => WmsOverlayParser::class,
		];

		$GLOBALS['wgParamDefinitions']['mapsimageoverlay'] = [
			'string-parser' => ImageOverlayParser::class,
		];

		if ( !$GLOBALS['egMapsDisableSmwIntegration'] && defined( 'SMW_VERSION' ) ) {
			SemanticMaps::newFromMediaWikiGlobals( $GLOBALS )->initExtension();
		}

		return true;
	}

	public static function onParserFirstCallInit1( Parser &$parser ) {
		$instance = new MapsCoordinates();
		return $instance->init( $parser );
	}

	public static function onParserFirstCallInit2( Parser &$parser ) {
		$instance = new MapsDisplayMap();
		return $instance->init( $parser );
	}

	public static function onParserFirstCallInit3( Parser &$parser ) {
		$instance = new MapsDistance();
		return $instance->init( $parser );
	}

	public static function onParserFirstCallInit4( Parser &$parser ) {
		$instance = new MapsFinddestination();
		return $instance->init( $parser );
	}

	public static function onParserFirstCallInit5( Parser &$parser ) {
		$instance = new MapsGeocode();
		return $instance->init( $parser );
	}

	public static function onParserFirstCallInit6( Parser &$parser ) {
		$instance = new MapsGeodistance();
		return $instance->init( $parser );
	}

	public static function onParserFirstCallInit7( Parser &$parser ) {
		$instance = new MapsMapsDoc();
		return $instance->init( $parser );
	}

	/**
	 * Adds a link to Admin Links page.
	 *
	 * @since 0.7
	 *
	 * @param ALTree $admin_links_tree
	 *
	 * @return boolean
	 */
	public static function addToAdminLinks( ALTree &$admin_links_tree ) {
		$displaying_data_section = $admin_links_tree->getSection( wfMessage( 'smw_adminlinks_displayingdata' )->text() );

		// Escape if SMW hasn't added links.
		if ( is_null( $displaying_data_section ) ) {
			return true;
		}

		$smw_docu_row = $displaying_data_section->getRow( 'smw' );

		$maps_docu_label = wfMessage( 'adminlinks_documentation', 'Maps' )->text();
		$smw_docu_row->addItem( AlItem::newFromExternalLink( 'https://semantic-mediawiki.org/wiki/Maps', $maps_docu_label ) );

		return true;
	}

	/**
	 * Adds global JavaScript variables.
	 *
	 * @since 1.0
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/MakeGlobalVariablesScript
	 * @param array &$vars Variables to be added into the output
	 * @param OutputPage $outputPage OutputPage instance calling the hook
	 * @return boolean true in all cases
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $outputPage ) {
		global $egMapsGlobalJSVars;

		$vars['egMapsDebugJS'] = $GLOBALS['egMapsDebugJS'];
                $vars[ 'egMapsAvailableServices' ] = $GLOBALS['egMapsAvailableServices'];

		$vars += $egMapsGlobalJSVars;

		return true;
	}

}

