<?php

declare( strict_types = 1 );

namespace Maps;

use AlItem;
use ALTree;
use Maps\GeoJsonPages\GeoJsonNewPageUi;
use Maps\Presentation\OutputFacade;
use MediaWiki\MediaWikiServices;
use SkinTemplate;
use SMWPrintRequest;

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

	public static function onSkinTemplateNavigationUniversal( SkinTemplate $skinTemplate, array &$links ) {
		if ( $skinTemplate->getTitle() === null ) {
			return true;
		}

		if ( $skinTemplate->getTitle()->getNamespace() === NS_GEO_JSON ) {
			if ( array_key_exists( 'edit', $links['views'] ) ) {
				$links['views']['edit']['text'] = wfMessage(
					$skinTemplate->getTitle()->exists() ? 'maps-geo-json-edit-source' : 'maps-geo-json-create-source'
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
			&& MediaWikiServices::getInstance()->getPermissionManager()->userHasRight( $article->getContext()->getUser(), 'createpage' );
	}

	public static function onRegisterTags( array &$tags ) {
		$tags[] = 'maps-visual-edit';
		return true;
	}

	public static function onChangeTagsAllowedAdd( array &$allowedTags, array $tags, \User $user = null ) {
		$allowedTags[] = 'maps-visual-edit';
	}

	/**
	 * Set the default format to 'map' when the requested properties are
	 * of type geographic coordinates.
	 *
	 * TODO: have a setting to turn this off and have it off by default for #show
	 *
	 * @since 1.0
	 *
	 * @param $format Mixed: The format (string), or false when not set yet
	 * @param SMWPrintRequest[] $printRequests The print requests made
	 *
	 * @return boolean
	 */
	public static function addGeoCoordsDefaultFormat( &$format, array $printRequests ) {
		// Only set the format when not set yet. This allows other extensions to override the Maps behavior.
		if ( $format === false ) {
			// Only apply when there is more then one print request.
			// This way requests comming from #show are ignored.
			if ( count( $printRequests ) > 1 ) {
				$allValid = true;
				$hasCoords = false;

				// Loop through the print requests to determine their types.
				foreach ( $printRequests as $printRequest ) {
					// Skip the first request, as it's the object.
					if ( $printRequest->getMode() == SMWPrintRequest::PRINT_THIS ) {
						continue;
					}

					$typeId = $printRequest->getTypeID();

					if ( $typeId == '_geo' ) {
						$hasCoords = true;
					} else {
						$allValid = false;
						break;
					}
				}

				// If they are all coordinates, set the result format to 'map'.
				if ( $allValid && $hasCoords ) {
					$format = 'map';
				}
			}

		}

		return true;
	}

	public static function addSmwSettings( array &$settings ) {
		// TODO: uncomment when it is safe for the semantic integration to be enabled by default
		// $settings['smwgNamespacesWithSemanticLinks'][NS_GEO_JSON] = true;
		return true;
	}

}
