window.mapsLeafletList = [];

(function( $, mw ) {
	function initializeMaps( $content ) {
		$content.find( '.maps-leaflet' ).each( function() {
			let $this = $( this );

			if ( $this.data( 'initialized' ) ) {
				return;
			}

			$this.data( 'initialized', true );

			let jqueryMap = $this.leafletmaps(
				JSON.parse( $this.find( 'div.mapdata' ).text() )
			);

			jqueryMap.setup();

			window.mapsLeafletList.push(jqueryMap);
		} );
	}

	mw.hook( 'wikipage.content' ).add( initializeMaps );

	mw.hook( 've.activationComplete' ).add( function() {
		let surface = ve.init.target.getSurface();
		// initializeMaps( $( surface.$element[0] ) );

		let thread = setInterval(
			function() {
				initializeMaps( $( surface.$element[0] ) );
			},
			1000
		);

		// mw.hook( 've.deactivationComplete' ).add( function() {
		// 	clearInterval( thread );
		// } );

		// surface.getModel().on( 'history', function() {
		// 	console.log('history');
		// 	initializeMaps( $( surface.$element[0] ) );
		// } );
	} );

})( window.jQuery, window.mediaWiki );
