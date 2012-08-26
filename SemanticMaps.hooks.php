<?php

/**
 * Static class for hooks handled by the Semantic Maps extension.
 * 
 * @since 0.7
 * 
 * @file SemanticMaps.hooks.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class SemanticMapsHooks {

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
	
	    $sm_docu_label = wfMessage( 'adminlinks_documentation', 'Semantic Maps' )->text();
	    $smw_docu_row->addItem( AlItem::newFromExternalLink( 'http://mapping.referata.com/wiki/Semantic_Maps', $sm_docu_label ) );
	
	    return true;		
	}

	/**
	 * Hook to add PHPUnit test cases.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
	 *
	 * @since 2.0
	 *
	 * @param array $files
	 *
	 * @return boolean
	 */
	public static function registerUnitTests( array &$files ) {
		$testFiles = array(
			'printers/KMLPrinter',
			'printers/MapPrinter',
		);

		foreach ( $testFiles as $file ) {
			$files[] = __DIR__ . '/tests/phpunit/' . $file . 'Test.php';
		}

		return true;
	}
	
}
