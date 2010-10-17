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
		parent::view();
		//$layer = $this->getLayer();
		//var_dump($layer);exit;
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
		$properties = array();

		foreach ( explode( "\n", $this->mContent ) as $line ) {
			$parts = explode( '=', $line, 2 );
			
			if ( count( $parts ) == 2 ) {
				$properties[$parts[0]] = $parts[1];
			}
		}
		
		return $properties;
	}
	
}