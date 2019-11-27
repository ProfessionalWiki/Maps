(function($, mw) {
	'use strict';

	function canEditPage(pageName) {
		let deferred = $.Deferred();

		new mw.Api().get({
			action: 'query',
			format: 'json',
			titles: pageName,
			prop: 'info',
			intestactions: 'edit'
		}).done(function(response) {
			// Next level usability in the MW API:
			let canEdit = response.query.pages[Object.keys(response.query.pages)[0]].actions.hasOwnProperty('edit');
			deferred.resolve(canEdit);
		});

		return deferred.promise();
	}

	if (!window.maps) {window.maps = {};}

	window.maps.api = {
		canEditPage: canEditPage
	};
})(window.jQuery, window.mediaWiki);
