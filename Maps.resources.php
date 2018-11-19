<?php

/**
 * Definition of Maps resource loader modules.
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Werner < daniel.a.r.werner@gmail.com >
 *
 * @codeCoverageIgnoreStart
 */
return call_user_func( function() {

	$pathParts = explode( '/', str_replace( DIRECTORY_SEPARATOR, '/', __DIR__ ) );

	$moduleTemplate = [
		'position' => 'top',
		'group' => 'ext.maps',
		'localBasePath' => __DIR__,
		'remoteExtPath' =>  end( $pathParts ),
		'targets' => [
			'mobile',
			'desktop'
		]
	];

	return [
		'ext.maps.common' => $moduleTemplate + [
			'messages' => [
				'maps-load-failed',
			] ,
			'scripts' => [
				'resources/ext.maps.common.js',
			],
		],

		'ext.maps.resizable' => $moduleTemplate + [
			'dependencies' => 'jquery.ui.resizable',
		],

		'mapeditor' => $moduleTemplate + [
			'scripts' => [
				'resources/editor/js/jquery.miniColors.js',
				'resources/editor/js/mapeditor.iefixes.js',
				'resources/editor/js/mapeditor.js',
			],
			'styles' => [
				'resources/editor/css/jquery.miniColors.css',
				'resources/editor/css/mapeditor.css',
			],
			'messages' => [
				'mapeditor-parser-error',
				'mapeditor-none-text',
				'mapeditor-done-button',
				'mapeditor-remove-button',
				'mapeditor-import-button',
				'mapeditor-export-button',
				'mapeditor-import-button2',
				'mapeditor-select-button',
				'mapeditor-mapparam-button',
				'mapeditor-clear-button',
				'mapeditor-imageoverlay-button',
			],
			'dependencies' => [
				'ext.maps.common',
				'jquery.ui.autocomplete',
				'jquery.ui.slider',
				'jquery.ui.dialog',
			],
		],

		'ext.maps.services' => $moduleTemplate + [
			'scripts' => [
				'resources/ext.maps.services.js',
			],
			'dependencies' => [
				'ext.maps.common',
			]
		]
	];

} );
// @codeCoverageIgnoreEnd
