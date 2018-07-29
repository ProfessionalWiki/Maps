window.maps = new ( function( $, mw ) {
	
	this.log = function( message ) {
		if ( mw.config.get( 'egMapsDebugJS' ) ) {
			mw.log( message );
		}
	};

	this.googlemapsList = [];
	this.leafletList = [];
	this.openlayersList = [];
} )( jQuery, mediaWiki );
