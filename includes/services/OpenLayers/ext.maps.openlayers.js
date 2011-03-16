/**
 * JavasSript for OpenLayers maps in the Maps extension.
 * @see http://www.mediawiki.org/wiki/Extension:Maps
 * 
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

jQuery(document).ready(function() {
	if ( true ) {
		OpenLayers.ImgPath = egMapsScriptPath + '/includes/services/OpenLayers/OpenLayers/img/';
	    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	    OpenLayers.Util.onImageLoadErrorColor = 'transparent';
		OpenLayers.Feature.prototype.popupClass = OpenLayers.Class(
			OpenLayers.Popup.FramedCloud,
			{
				'autoSize': true,
				'minSize': new OpenLayers.Size( 200, 100 )
			}
		);
		
		// OpenLayers.Lang.setCode( params.langCode );
		
		for ( i in window.maps.openlayers ) {
			jQuery( '#' + i ).openlayers( i, window.maps.openlayers[i] );
		}
	}
	else {
		alert( mediaWiki.msg( 'maps-openlayers-incompatbrowser' ) );
		
		for ( i in window.maps.googlemaps3 ) {
			jQuery( '#' + i ).text( mediaWiki.msg( 'maps-load-failed' ) );
		}
	}	
});
