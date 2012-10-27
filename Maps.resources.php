<?php

/**
 * Definition of Maps resource loader modules.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 3.0
 *
 * @file
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 *
 * @codeCoverageIgnoreStart
 */
return call_user_func( function() {

	$moduleTemplate = array(
		'localBasePath' => __DIR__ . '/includes',
		'remoteExtPath' =>  'Maps/includes',
		'group' => 'ext.maps'
	);

	return array(
		'ext.maps.common' => $moduleTemplate + array(
			'messages' => array(
				'maps-load-failed',
			) ,
			'scripts' => array(
				'ext.maps.common.js',
			),
		),

		'ext.maps.coord' => $moduleTemplate + array(
			'messages' => array(
				'maps-abb-north',
				'maps-abb-east',
				'maps-abb-south',
				'maps-abb-west',
			),
			'scripts' => array(
				'ext.maps.coord.js'
			),
		),

		'ext.maps.resizable' => $moduleTemplate + array(
			'dependencies' => 'jquery.ui.resizable',
		),

		'mapeditor' => $moduleTemplate + array(
			'scripts' => array(
				'js/jquery.miniColors.js',
				'js/mapeditor.iefixes.js',
				'js/mapeditor.js',
			),
			'styles' => array(
				'css/jquery.miniColors.css',
				'css/mapeditor.css',
			),
			'messages' => array(
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
			),
			'dependencies' => array(
				'ext.maps.common',
				'jquery.ui.autocomplete',
				'jquery.ui.slider',
				'jquery.ui.dialog',
			),
		),
	);

} );
// @codeCoverageIgnoreEnd
