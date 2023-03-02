<?php

declare( strict_types = 1 );

namespace Maps\LegacyMapEditor;

use Maps\GoogleMapsService;
use SpecialPage;

/**
 * Special page with map editor interface using Google Maps.
 *
 * @since 2.0
 *
 * @licence GNU GPL v2+
 * @author Kim Eik
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpecialMapEditor extends SpecialPage {

	/**
	 * @see SpecialPage::__construct
	 *
	 * @since 2.0
	 */
	public function __construct() {
		parent::__construct( 'MapEditor' );
	}

	/**
	 * @see SpecialPage::execute
	 *
	 * @since 2.0
	 *
	 * @param null|string $subPage
	 */
	public function execute( $subPage ) {
		$this->setHeaders();

		$outputPage = $this->getOutput();

		$outputPage->addHtml(
			GoogleMapsService::getApiScript(
				$this->getLanguage()->getCode(),
				[ 'libraries' => 'drawing' ]
			)
		);

		$outputPage->addModules( [ 'ext.maps.wikitext.editor' ] );
		$editorHtml = new MapEditorHtml( $this->getAttribs() );
		$html = $editorHtml->getEditorHtml();
		$outputPage->addHTML( $html );
	}

	/**
	 * @since 2.1
	 *
	 * @return array
	 */
	protected function getAttribs() {
		return [
			'id' => 'map-canvas',
			'context' => 'Maps\MediaWiki\Specials\SpecialMapEditor'
		];
	}

}
