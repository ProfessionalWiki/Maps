<?php

/**
 * Initialization file for the Maps extension.
 *
 * @links https://github.com/JeroenDeDauw/Maps/blob/master/README.md#maps Documentation
 * @links https://github.com/JeroenDeDauw/Maps/issues Support
 * @links https://github.com/JeroenDeDauw/Maps Source code
 *
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

use DataValues\Geo\Parsers\GeoCoordinateParser;
use FileFetcher\SimpleFileFetcher;
use Maps\CircleParser;
use Maps\DistanceParser;
use Maps\SemanticMaps;
use Maps\ImageOverlayParser;
use Maps\LineParser;
use Maps\LocationParser;
use Maps\PolygonParser;
use Maps\RectangleParser;
use Maps\ServiceParam;
use Maps\WmsOverlayParser;


if ( version_compare( $GLOBALS['wgVersion'], '1.28c' , '<' ) ) {
	throw new Exception(
		'This version of Maps requires MediaWiki 1.28 or above; use Maps 3.5.x for older versions.'
		. ' More information at https://github.com/JeroenDeDauw/Maps/blob/master/INSTALL.md'
	);
}

if ( version_compare( $GLOBALS['wgVersion'], '1.28c', '>' ) ) {
	if ( function_exists( 'wfLoadExtension' ) ) {
		wfLoadExtension( 'Maps' );
		// Keep i18n globals so mergeMessageFileList.php doesn't break
		$GLOBALS['wgMessagesDirs']['Maps']							= __DIR__ . '/i18n';
		$GLOBALS['wgExtensionMessagesFiles']['MapsMagic'] 			= __DIR__ . '/Maps.i18n.magic.php';
		$GLOBALS['wgExtensionMessagesFiles']['MapsNamespaces'] 		= __DIR__ . '/Maps.i18n.namespaces.php';
		$GLOBALS['wgExtensionMessagesFiles']['MapsAlias'] 			= __DIR__ . '/Maps.i18n.alias.php';
		/* wfWarn(
			'Deprecated PHP entry point used for Maps extension. ' .
			'Please use wfLoadExtension instead, ' .
			'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
		); */
		return;
	}
}
