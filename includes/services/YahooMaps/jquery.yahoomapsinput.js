/**
 * JavasSript for the Yahoo! Maps form input of the Semantic Maps extension.
 * @see http://www.mediawiki.org/wiki/Extension:Semantic_Maps
 * 
 * @since 0.8
 * @ingroup SemanticMaps
 * 
 * @licence GNU GPL v3
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

(function( $ ){ $.fn.yahoomapsinput = function( options ) {

	var self = this;
	var geocoder = false;

	/**
	 * Creates and places a new marker on the map at the provided
	 * coordinate set and the pans to it.
	 * @param {Object} coordinate
	 */
	this.showCoordinate = function( coordinate ) {
        // TODO
    };

	this.setupGeocoder = function() {
		if ( geocoder === false ) {
			// TODO
		}
	}

	this.geocodeAddress = function( address ) {
		this.setupGeocoder();
		// TODO
	};

	this.mapforminput( mapDivId, options );

	this.mapDiv.yahoomaps( options );
	
	return this;
	
}; })( jQuery );