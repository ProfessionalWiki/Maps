<?php
  
/**
 * Initialization file for the Semantic Maps extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Semantic_Maps
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

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define('SM_VERSION', '0.5 a14');

$smgScriptPath 	= $wgScriptPath . '/extensions/SemanticMaps';
$smgIP 			= $IP . '/extensions/SemanticMaps';

$smgStyleVersion = $wgStyleVersion . '-' . SM_VERSION;

// Include the settings file.
require_once($smgIP . '/SM_Settings.php');

$wgExtensionFunctions[] = 'smfSetup'; 

$wgHooks['AdminLinks'][] = 'smfAddToAdminLinks';

$wgExtensionMessagesFiles['SemanticMaps'] = $smgIP . '/SemanticMaps.i18n.php';

// Registration of the Geographical Coordinate type.
$wgAutoloadClasses['SMGeoCoordsValue'] = $smgIP . '/SM_GeoCoordsValue.php';
$wgHooks['smwInitDatatypes'][] = 'smfInitGeoCoordsType';

/**
 * 'Initialization' function for the Semantic Maps extension. 
 * The only work done here is creating the extension credits for
 * Semantic Maps. The actuall work in done via the Maps hooks.
 */
function smfSetup() {
	global $wgExtensionCredits, $wgLang, $egMapsServices;
	
	// Creation of a list of internationalized service names.
	$services = array();
	foreach (array_keys($egMapsServices) as $name) $services[] = wfMsg('maps_'.$name);
	$services_list = $wgLang->listToText($services);	

	wfLoadExtensionMessages( 'SemanticMaps' );

	$wgExtensionCredits['other'][]= array(
		'path' => __FILE__,
		'name' => wfMsg('semanticmaps_name'),
		'version' => SM_VERSION,
		'author' => array('[http://bn2vs.com Jeroen De Dauw]', '[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]', 'others'),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Maps',
		'description' => wfMsgExt( 'semanticmaps_desc', 'parsemag', $services_list ),
		'descriptionmsg' => wfMsgExt( 'semanticmaps_desc', 'parsemag', $services_list ),
	);

	return true;	
}

/**
 * Adds support for the geographical coordinate data type to Semantic MediaWiki.
 */
function smfInitGeoCoordsType() {
	SMWDataValueFactory::registerDatatype('_geo', 'SMGeoCoordsValue');
	return true;	
}

/**
 * Adds a link to Admin Links page.
 */
function smfAddToAdminLinks(&$admin_links_tree) {
    $displaying_data_section = $admin_links_tree->getSection(wfMsg('smw_adminlinks_displayingdata'));

    // Escape if SMW hasn't added links.
    if (is_null($displaying_data_section)) return true;

    $smw_docu_row = $displaying_data_section->getRow('smw');

    $sm_docu_label = wfMsg('adminlinks_documentation', wfMsg('semanticmaps_name'));
    $smw_docu_row->addItem(AlItem::newFromExternalLink("http://www.mediawiki.org/wiki/Extension:Semantic_Maps", $sm_docu_label));

    return true;
}

