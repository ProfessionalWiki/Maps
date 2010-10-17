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
	}
	
	/**
	 * Returns a new MapsLayer object created from the data in the page.
	 * 
	 * @since 0.7.1
	 * 
	 * @return MapsLayer
	 */
	public function getLayer() {
		return MapsLayer::newFromArray();
	}
	
}