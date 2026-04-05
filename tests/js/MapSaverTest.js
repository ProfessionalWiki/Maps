( function () {
	'use strict';

	QUnit.module( 'Maps.MapSaver' );

	QUnit.test( 'MapSaver constructor returns an object with a save method', function ( assert ) {
		var saver = window.maps.MapSaver( 'TestPage' );

		assert.strictEqual( typeof saver, 'object', 'MapSaver returns an object' );
		assert.strictEqual( typeof saver.save, 'function', 'Object has a save method' );
	} );

	QUnit.module( 'Maps.MapSaver.save', {
		beforeEach: function () {
			this.originalGetRights = mw.user.getRights;
			this.OriginalApi = mw.Api;
			this.originalAlert = window.alert;

			mw.user.getRights = function ( callback ) {
				callback( [ 'applychangetags' ] );
			};
			mw.Api = function () {
				return {
					edit: function () {
						return $.Deferred().reject( 'API error' ).promise();
					}
				};
			};

			this.alertMessages = [];
			var self = this;
			window.alert = function ( msg ) {
				self.alertMessages.push( msg );
			};
		},
		afterEach: function () {
			mw.user.getRights = this.originalGetRights;
			mw.Api = this.OriginalApi;
			window.alert = this.originalAlert;
		}
	} );

	QUnit.test( 'save calls onError when API edit fails', function ( assert ) {
		var done = assert.async();
		var saver = window.maps.MapSaver( 'TestPage' );

		saver.save( {
			newContent: '{}',
			summary: 'test',
			done: function () {
				assert.true( false, 'done should not be called on failure' );
				done();
			},
			onError: function () {
				assert.true( true, 'onError callback was called' );
				done();
			}
		} );

		setTimeout( function () {
			assert.true( false, 'Neither done nor onError was called' );
			done();
		}, 3000 );
	} );

	QUnit.test( 'save alerts user when API edit fails', function ( assert ) {
		var done = assert.async();
		var alertMessages = this.alertMessages;
		var saver = window.maps.MapSaver( 'TestPage' );

		saver.save( {
			newContent: '{}',
			summary: 'test',
			done: function () {}
		} );

		setTimeout( function () {
			assert.strictEqual( alertMessages.length, 1, 'One alert was shown' );
			done();
		}, 100 );
	} );

}() );
