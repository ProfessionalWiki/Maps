/*global jQuery, mediaWiki, maps */
/*global confirm */
( function ( $, mw, maps ) {
	'use strict';

	/**
	 * @since 3.5
	 *
	 * @param {object} container
	 * @return {this}
	 */
	var services = function ( container ) {

		if ( $.type( container ) !== 'object' ) {
			throw new Error( 'The container is not of the correct type ' + $.type( container ) );
		}

		this.container = container;

		return this;
	};

	/* Public methods */

	services.prototype = {

		constructor: services,

		/**
		 * @since 3.5
		 *
		 * @param {string} service
		 */
		render: function( service ) {
			if ( service === 'googlemaps' || service === 'maps' || service === 'googlemaps3' ) {
				this.google();
			}

			if ( service === 'leaflet' || service === 'leafletmaps' ) {
				this.leaflet();
			}
		},

		/**
		 * Google service
		 *
		 * @since 3.5
		 */
		google: function() {

			var self = this;

			// https://www.mediawiki.org/wiki/ResourceLoader/Modules#mw.loader.using
			mw.loader.using( 'ext.maps.googlemaps3' ).done( function () {

				if ( typeof google === 'undefined' ) {
					throw new Error( 'The google map service is unknown, please ensure that the API or module is loaded correctly.' );
				}

				self.container.find( '.maps-googlemaps3' ).each( function() {
					var $this = $( this );
					$this.googlemaps( $.parseJSON( $this.find( 'div').text() ) );
				} );
			} );
		},

		/**
		 * Leaflet service
		 *
		 * @since 3.5
		 */
		leaflet: function() {
			mw.loader.using( 'ext.maps.leaflet' ).done( function () {
				$( '.maps-leaflet' ).each( function() {
					var $this = $( this );
					maps.leafletList.push(
						$this.leafletmaps( $.parseJSON( $this.find( 'div').text() ) )
					);
				} );
			} );
		}
	};

	maps.services = services;

}( jQuery, mediaWiki, maps ) );
