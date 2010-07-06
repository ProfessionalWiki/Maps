<?php

/**
 * Initialization file for the Semantic Maps extension.
 * Extension documentation: http://mapping.referata.com/wiki/Semantic_Maps
 *
 * @file SemanticMaps.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw 
 */

/**
 * This documenation group collects source code files belonging to Semantic Maps.
 *
 * Please do not use this group name for other code. If you have an extension to 
 * Semantic Maps, please use your own group defenition.
 * 
 * @defgroup SemanticMaps Semantic Maps
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

// Show a warning if Maps is not loaded.
if ( ! defined( 'Maps_VERSION' ) ) {
	echo '<b>Warning:</b> You need to have <a href="http://www.mediawiki.org/wiki/Extension:Maps">Maps</a> installed in order to use <a href="http://www.mediawiki.org/wiki/Extension:Semantic Maps">Semantic Maps</a>. ';
}

// Show a warning if Semantic MediaWiki is not loaded.
if ( ! defined( 'SMW_VERSION' ) ) {
	echo '<b>Warning:</b> You need to have <a href="http://semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> installed in order to use <a href="http://www.mediawiki.org/wiki/Extension:Semantic Maps">Semantic Maps</a>.';
}

// Only initialize the extension when all dependencies are present.
if ( defined( 'Maps_VERSION' ) && defined( 'SMW_VERSION' ) ) {
	define( 'SM_VERSION', '0.6.4 rc1' );

	$useExtensionPath = version_compare( $wgVersion, '1.16', '>=' ) && isset( $wgExtensionAssetsPath ) && $wgExtensionAssetsPath;
	$smgScriptPath 	= ( $useExtensionPath ? $wgExtensionAssetsPath : $wgScriptPath . '/extensions' ) . '/SemanticMaps';	
	$smgDir 		= dirname( __FILE__ ) . '/';
	unset( $useExtensionPath );

	$smgStyleVersion = $wgStyleVersion . '-' . SM_VERSION;

	// Include the settings file.
	require_once( $smgDir . 'SM_Settings.php' );

	$wgExtensionFunctions[] = 'smfSetup';

	$wgHooks['AdminLinks'][] = 'smfAddToAdminLinks';

	$wgExtensionMessagesFiles['SemanticMaps'] = $smgDir . 'SemanticMaps.i18n.php';

	// Include the GeoCoords related functionality.
	require_once( $smgDir . '/GeoCoords/SM_GeoCoords.php' );
}

/**
 * 'Initialization' function for the Semantic Maps extension. 
 * The only work done here is creating the extension credits for
 * Semantic Maps. The actuall work in done via the Maps hooks.
 * 
 * @since 0.1
 * 
 * @return true
 */
function smfSetup() {
	global $wgExtensionCredits, $wgLang, $wgOut, $egMapsServices, $smgScriptPath;

	// Creation of a list of internationalized service names.
	$services = array();
	foreach ( array_keys( $egMapsServices ) as $name ) $services[] = wfMsg( 'maps_' . $name );
	$services_list = $wgLang->listToText( $services );

	// This function has been deprecated in 1.16, but needed for earlier versions.
	// It's present in 1.16 as a stub, but lets check if it exists in case it gets removed at some point.
	if ( function_exists( 'wfLoadExtensionMessages' ) ) {
		wfLoadExtensionMessages( 'SemanticMaps' );
	}

	$wgExtensionCredits['other'][] = array(
		'path' => __FILE__,
		'name' => wfMsg( 'semanticmaps_name' ),
		'version' => SM_VERSION,
		'author' => array(
			'[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
			'[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]',
			'[http://www.ohloh.net/p/semanticmaps/contributors others]'
		),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Maps',
		'description' => wfMsgExt( 'semanticmaps_desc', 'parsemag', $services_list ),
	);

	return true;
}

/**
 * Adds a link to Admin Links page.
 * 
 * @since 0.2
 * 
 * @return true
 */
function smfAddToAdminLinks( &$admin_links_tree ) {
    $displaying_data_section = $admin_links_tree->getSection( wfMsg( 'smw_adminlinks_displayingdata' ) );

    // Escape if SMW hasn't added links.
    if ( is_null( $displaying_data_section ) ) return true;

    $smw_docu_row = $displaying_data_section->getRow( 'smw' );

    $sm_docu_label = wfMsg( 'adminlinks_documentation', wfMsg( 'semanticmaps_name' ) );
    $smw_docu_row->addItem( AlItem::newFromExternalLink( "http://www.mediawiki.org/wiki/Extension:Semantic_Maps", $sm_docu_label ) );

    return true;
}