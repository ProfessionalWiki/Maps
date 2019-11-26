(function($, mw, L) {
	'use strict';

	if (!window.maps) {window.maps = {};}
	if (!window.maps.leaflet) {window.maps.leaflet = {};}

	window.maps.leaflet.LeafletCluster = {
		newLayer: function(options) {
			return new L.MarkerClusterGroup({
				maxClusterRadius: options.clustermaxradius,
				disableClusteringAtZoom: options.clustermaxzoom + 1,
				zoomToBoundsOnClick: options.clusterzoomonclick,
				spiderfyOnMaxZoom: options.clusterspiderfy,
				iconCreateFunction: function(cluster) {
					var childCount = cluster.getChildCount();

					var imagePath = mw.config.get( 'egMapsScriptPath' ) + '/resources/leaflet/cluster/';

					var styles = [
						{
							iconUrl: imagePath + 'm1.png',
							iconSize: [53, 52]
						},
						{
							iconUrl: imagePath + 'm2.png',
							iconSize: [56, 55]
						},
						{
							iconUrl: imagePath + 'm3.png',
							iconSize: [66, 65]
						},
						{
							iconUrl: imagePath + 'm4.png',
							iconSize: [78, 77]
						},
						{
							iconUrl: imagePath + 'm5.png',
							iconSize: [90, 89]
						}
					];

					var index = 0;
					var dv = childCount;
					while (dv !== 0) {
						dv = parseInt(dv / 10, 10);
						index++;
					}
					var index = Math.min(index, styles.length);
					index = Math.max(0, index - 1);
					index = Math.min(styles.length - 1, index);
					var style = styles[index];

					return new L.divIcon({
						iconSize: style.iconSize,
						className: '',
						html: '<img style="' +
							'" src="' + style.iconUrl + '" />' +
							'<span style="' +
							'position: absolute; font-size: 11px; font-weight: bold; text-align: center; ' +
							'top: 0; left: 0; ' +
							'line-height: ' + style.iconSize[1] + 'px;' +
							'width: ' + style.iconSize[0] + 'px;' +
							'">' + childCount + '</span>'
					});
				}
			});
		}
	};
})(window.jQuery, window.mediaWiki, window.L);
