<?php

/**
 * Initialization file for the Maps extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Maps
 *
 * @file Maps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

/**
 * This documenation group collects source code files belonging to Maps.
 *
 * Please do not use this group name for other code. If you have an extension to
 * Maps, please use your own group defenition.
 *
 * @defgroup Maps Maps
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

// Include the Validator extension if that hasn't been done yet, since it's required for Maps to work.
if ( !defined( 'Validator_VERSION' ) ) {
	@include_once( dirname( __FILE__ ) . '/../Validator/Validator.php' );
}

// Only initialize the extension when all dependencies are present.
if ( ! defined( 'Validator_VERSION' ) ) {
	echo '<b>Warning:</b> You need to have <a href="http://www.mediawiki.org/wiki/Extension:Validator">Validator</a> installed in order to use <a href="http://www.mediawiki.org/wiki/Extension:Maps">Maps</a>.';
}
else {
	define( 'Maps_VERSION', '0.6.4 a6' );

	// The different coordinate notations.
	define( 'Maps_COORDS_FLOAT', 'float' );
	define( 'Maps_COORDS_DMS', 'dms' );
	define( 'Maps_COORDS_DM', 'dm' );
	define( 'Maps_COORDS_DD', 'dd' );

	// The symbols to use for degrees, minutes and seconds.
	define( 'Maps_GEO_DEG', '°' );
	define( 'Maps_GEO_MIN', "'" );
	define( 'Maps_GEO_SEC', '"' );

	$useExtensionPath = version_compare( $wgVersion, '1.16', '>=' ) && isset( $wgExtensionAssetsPath ) && $wgExtensionAssetsPath;
	$egMapsScriptPath 	= ( $useExtensionPath ? $wgExtensionAssetsPath : $wgScriptPath . '/extensions' ) . '/Maps';
	$egMapsDir 			= dirname( __FILE__ ) . '/';
	unset( $useExtensionPath );

	// To ensure Maps remains compatible with pre 1.16.
	if ( version_compare( $wgVersion, '1.16', '<' ) ) {
		$wgAutoloadClasses['Html'] = $egMapsDir . 'Compat/Html.php';
	}

	$egMapsStyleVersion = $wgStyleVersion . '-' . Maps_VERSION;

	$egMapsFeatures = array();
	$egMapsServices = array();

	// Include the settings file.
	require_once $egMapsDir . 'Maps_Settings.php';

	$wgExtensionMessagesFiles['Maps'] = $egMapsDir . 'Maps.i18n.php';

	if ( version_compare( $wgVersion, '1.16alpha', '>=' ) ) {
		$wgExtensionMessagesFiles['MapsMagic'] = $egMapsDir . 'Maps.i18n.magic.php';
	}

	// Register the initialization function of Maps.
	$wgExtensionFunctions[] = 'efMapsSetup';

	$wgHooks['AdminLinks'][] = 'efMapsAddToAdminLinks';
}

/**
 * Initialization function for the Maps extension.
 */
function efMapsSetup() {
	global $wgExtensionCredits, $wgLang, $wgAutoloadClasses;
	global $egMapsDefaultService, $egMapsAvailableServices, $egMapsServices;
	global $egMapsDir, $egMapsUseMinJs, $egMapsJsExt;

	// Autoload the includes/ classes.
	$wgAutoloadClasses['MapsMapper'] 				= $egMapsDir . 'Includes/Maps_Mapper.php';
	$wgAutoloadClasses['MapsCoordinateParser'] 		= $egMapsDir . 'Includes/Maps_CoordinateParser.php';
	$wgAutoloadClasses['MapsDistanceParser'] 		= $egMapsDir . 'Includes/Maps_DistanceParser.php';
	
	// This function has been deprecated in 1.16, but needed for earlier versions.
	// It's present in 1.16 as a stub, but lets check if it exists in case it gets removed at some point.
	if ( function_exists( 'wfLoadExtensionMessages' ) ) {
		wfLoadExtensionMessages( 'Maps' );
	}

	// Load the service/ classes and interfaces.
	require_once $egMapsDir . 'Services/Maps_iMappingService.php';
	$wgAutoloadClasses['MapsMappingService'] = $egMapsDir . 'Services/Maps_MappingService.php';
	
	wfRunHooks( 'MappingServiceLoad' );
	
	wfRunHooks( 'MappingFeatureLoad' );

	// Remove all hooked in services that should not be available.
	foreach ( $egMapsServices as $service => $data ) {
		if ( !in_array( $service, $egMapsAvailableServices ) ) unset( $egMapsServices[$service] );
	}
	$egMapsAvailableServices = array_keys( $egMapsServices );

	// Enure that the default service is one of the enabled ones.
	$egMapsDefaultService = in_array( $egMapsDefaultService, $egMapsAvailableServices ) ? $egMapsDefaultService : $egMapsAvailableServices[0];

	// Creation of a list of internationalized service names.
	$services = array();
	foreach ( array_keys( $egMapsServices ) as $name ) $services[] = wfMsg( 'maps_' . $name );
	$services_list = $wgLang->listToText( $services );

	$wgExtensionCredits['parserhook'][] = array(
		'path' => __FILE__,
		'name' => wfMsg( 'maps_name' ),
		'version' => Maps_VERSION,
		'author' => array(
			'[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
			'[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]',
			'[http://www.ohloh.net/p/maps/contributors others]'
		),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Maps',
		'description' => wfMsgExt( 'maps_desc', 'parsemag', $services_list ),
	);

	MapsMapper::initialize();

	$egMapsJsExt = $egMapsUseMinJs ? '.min.js' : '.js';

	return true;
}

/**
 * Adds a link to Admin Links page.
 */
function efMapsAddToAdminLinks( &$admin_links_tree ) {
    $displaying_data_section = $admin_links_tree->getSection( wfMsg( 'smw_adminlinks_displayingdata' ) );

    // Escape if SMW hasn't added links.
    if ( is_null( $displaying_data_section ) ) return true;
    $smw_docu_row = $displaying_data_section->getRow( 'smw' );

    $maps_docu_label = wfMsg( 'adminlinks_documentation', wfMsg( 'maps_name' ) );
    $smw_docu_row->addItem( AlItem::newFromExternalLink( 'http://www.mediawiki.org/wiki/Extension:Maps', $maps_docu_label ) );

    return true;
}