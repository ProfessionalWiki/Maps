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

	function getLatestRevision(pageName) {
		let deferred = $.Deferred();

		new mw.Api().post({
			action: 'query',
			prop: 'revisions',
			rvlimit: 1,
			rvprop: [ 'ids', 'content' ],
			titles: pageName
		}).done(function(response) {
			deferred.resolve(response.query.pages[Object.keys(response.query.pages)[0]].revisions[0]);
		});

		return deferred.promise();
	}

	function purgePage(pageName) {
		new mw.Api().post({
			action: 'purge',
			titles: pageName
		})
	}

	if (!window.maps) {window.maps = {};}

	window.maps.api = {
		canEditPage: canEditPage,
		getLatestRevision: getLatestRevision,
		purgePage: purgePage
	};
})(window.jQuery, window.mediaWiki);
