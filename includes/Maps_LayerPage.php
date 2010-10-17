<?php

/**
 * Special handling for image description pages
 *
 * @since 0.7.1
 * 
 * @file Maps_LayerPage.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsLayerPage extends Article {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.7.1
	 * 
	 * @param Title $title
	 */
	public function __construct( Title $title ) {
		parent::__construct( $title );
	}
	
	/**
	 * @see Article::view
	 * 
	 * @since 0.7.1
	 */
	public function view() {
		global $wgOut;
		
		$wgOut->setPageTitle( $this->mTitle->getPrefixedText() );
		
		$rows = array();
		
		$rows[] = Html::rawElement(
			'tr',
			array(),
			Html::element(
				'th',
				array( 'width' => '200px' ),
				wfMsg( 'maps-layer-property' )
			) .
			Html::element(
				'th',
				array(),
				wfMsg( 'maps-layer-value' )
			)
		);		
		
		foreach ( $this->getProperties() as $property => $value ) {
			$rows[] = Html::rawElement(
				'tr',
				array(),
				Html::element(
					'td',
					array(),
					$property
				) .
				Html::element(
					'td',
					array(),
					$value
				)			
			);			
		}
		
		$wgOut->addHTML( Html::rawElement( 'table', array( 'width' => '100%', 'class' => 'wikitable sortable' ), implode( "\n", $rows ) ) );
	}
	
	/**
	 * Returns a new MapsLayer object created from the data in the page.
	 * 
	 * @since 0.7.1
	 * 
	 * @return MapsLayer
	 */
	public function getLayer() {
		return MapsLayer::newFromArray( $this->getProperties() );
	}
	
	/**
	 * Returns the properties defined on the page.
	 * 
	 * @since 0.7.1
	 * 
	 * @return array
	 */
	protected function getProperties() {
		static $cachedProperties = false;
		
		if ( $cachedProperties !== false ) {
			return $cachedProperties;
		}
		
		$properties = array();

		if ( is_null( $this->mContent ) ) {
			$this->loadContent();
		}
		
		foreach ( explode( "\n", $this->mContent ) as $line ) {
			$parts = explode( '=', $line, 2 );
			
			if ( count( $parts ) == 2 ) {
				$properties[strtolower( str_replace( ' ', '', $parts[0] ) )] = $parts[1];
			}
		}

		$cachedProperties = $properties;
		return $properties;
	}
	
}