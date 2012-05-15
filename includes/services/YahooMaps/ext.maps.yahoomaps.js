/**
 * JavaScript for Yahoo! Maps maps in the Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

(function( $ ) {

	$( document ).ready( function() {

		$( '.maps-yahoomaps' ).each( function() {
			var $this = $( this );
			$this.yahoomaps( $this.attr( 'id' ), jQuery.parseJSON( $this.find( 'div').text() ) );
		} );

	} );

})( window.jQuery );