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
	 * Cached MapsLayer or false.
	 * 
	 * @since 0.7.1
	 * 
	 * @var false or MapsLayer
	 */
	protected $cachedLayer = false;
	
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
		
		$layer = $this->getLayer();
		
		if ( !$layer->isValid() ) {
			$wgOut->addHTML(
				'<span class="errorbox">' .
				htmlspecialchars( wfMsg( 'maps-error-invalid-layerdef' ) ) . 
				'</span><br />'
			);
		}
		
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
		
		foreach ( $layer->getProperties() as $property => $value ) {
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
	 * Returns if the layer definition in the page is valid.
	 * 
	 * @since 0.7.1
	 * 
	 * @return boolean
	 */
	public function hasValidDefinition() {
		$layer = $this->getLayer();
		return $layer->isValid();
	}
	
	/**
	 * Returns a new MapsLayer object created from the data in the page.
	 * 
	 * @since 0.7.1
	 * 
	 * @return MapsLayer
	 */
	public function getLayer() {
		if ( $this->cachedLayer === false ) {
			$this->cachedLayer = MapsLayer::newFromArray( $this->getProperties() );
		}		
		
		return $this->cachedLayer;
	}
	
	/**
	 * Returns the properties defined on the page.
	 * 
	 * @since 0.7.1
	 * 
	 * @return array
	 */
	protected function getProperties() {
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

		$properties['type'] = array_key_exists( 'type', $properties ) ? $properties['type'] : MapsLayer::getDefaultType();
		
		return $properties;
	}
	
}