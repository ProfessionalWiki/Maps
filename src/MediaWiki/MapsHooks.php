<?php


namespace Maps\MediaWiki;

use AlItem;
use ALTree;
use Maps\Presentation\GeoJsonNewPageUi;
use Maps\Presentation\OutputFacade;
use ParserOptions;
use Revision;
use SkinTemplate;
use SMW\ApplicationFactory;
use SMW\DIProperty;
use User;
use WikiPage;

/**
 * Static class for hooks handled by the Maps extension.
 *
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsHooks {

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
		$displaying_data_section = $admin_links_tree->getSection(
			wfMessage( 'smw_adminlinks_displayingdata' )->text()
		);

		// Escape if SMW hasn't added links.
		if ( is_null( $displaying_data_section ) ) {
			return true;
		}

		$smw_docu_row = $displaying_data_section->getRow( 'smw' );

		$maps_docu_label = wfMessage( 'adminlinks_documentation', 'Maps' )->text();
		$smw_docu_row->addItem(
			AlItem::newFromExternalLink( 'https://www.semantic-mediawiki.org/wiki/Extension:Maps', $maps_docu_label )
		);

		return true;
	}

	/**
	 * Adds global JavaScript variables.
	 *
	 * @since 1.0
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/MakeGlobalVariablesScript
	 *
	 * @param array &$vars Variables to be added into the output
	 *
	 * @return boolean true in all cases
	 */
	public static function onMakeGlobalVariablesScript( array &$vars ) {
		$vars['egMapsScriptPath'] = $GLOBALS['wgScriptPath'] . '/extensions/Maps/'; // TODO: wgExtensionDirectory?
		$vars['egMapsDebugJS'] = $GLOBALS['egMapsDebugJS'];
		$vars['egMapsAvailableServices'] = $GLOBALS['egMapsAvailableServices'];
		$vars['egMapsLeafletLayersApiKeys'] = $GLOBALS['egMapsLeafletLayersApiKeys'];

		$vars += $GLOBALS['egMapsGlobalJSVars'];

		return true;
	}

	public static function onSkinTemplateNavigation( SkinTemplate $skinTemplate, array &$links ) {
		if ( $skinTemplate->getTitle() === null ) {
			return true;
		}

		if ( $skinTemplate->getTitle()->getNamespace() === NS_GEO_JSON ) {
			if ( array_key_exists( 'edit', $links['views'] ) ) {
				$links['views']['edit']['text'] = wfMessage(
					$skinTemplate->getTitle()->exists() ? 'maps-geo-json-edit-source': 'maps-geo-json-create-source'
				);
			}
		}

		return true;
	}

	public static function onBeforeDisplayNoArticleText( \Article $article ) {
		return !self::shouldShowGeoJsonCreatePageUi( $article );
	}

	public static function onShowMissingArticle( \Article $article ) {
		if ( self::shouldShowGeoJsonCreatePageUi( $article ) ) {
			$ui = new GeoJsonNewPageUi( OutputFacade::newFromOutputPage( $article->getContext()->getOutput() ) );
			$ui->addToOutput();
		}

		return true;
	}

	private static function shouldShowGeoJsonCreatePageUi( \Article $article ): bool {
		return $article->getTitle()->getNamespace() === NS_GEO_JSON
			&& $article->getContext()->getUser()->isAllowed( 'createpage' );
	}

	public static function onRegisterTags( array &$tags ) {
		$tags[] = 'maps-visual-edit';
		return true;
	}

	public static function onChangeTagsAllowedAdd( array &$allowedTags, array $tags, \User $user = null ) {
		$allowedTags[] = 'maps-visual-edit';
	}

	public static function onResourceLoaderTestModules( array &$modules, $resourceLoader ) {
		$modules['qunit']['ext.maps.test'] = [
			'scripts' => [
				'tests/js/leaflet/GeoJsonTest.js',
			],
			'dependencies' => [
				'ext.maps.leaflet.geojson',
			],
			'localBasePath' => __DIR__ . '/../../',
			'remoteExtPath' => 'Maps'
		];
	}

}
