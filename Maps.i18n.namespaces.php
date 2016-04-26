<?php

$namespaceNames = [];

// For wikis without Maps installed.
if ( !defined( 'Maps_NS_LAYER' ) ) {
	define( 'Maps_NS_LAYER', 420 );
	define( 'Maps_NS_LAYER_TALK', 421 );
}

$namespaceNames['en'] = [
	Maps_NS_LAYER       => 'Layer',
	Maps_NS_LAYER_TALK  => 'Layer_talk',
];

$namespaceNames['de'] = [
	Maps_NS_LAYER_TALK  => 'Layer_Diskussion',
];
