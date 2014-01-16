<?php

/**
 * File defining the settings for the Semantic Maps extension.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}



# Mapping services configuration

	# Array of String. The default mapping service for each feature, which will be used when no valid service is provided by the user.
	# Each service needs to be enabled, if not, the first one from the available services will be taken.
	# Note: The default service needs to be available for the feature you set it for, since it's used as a fallback mechanism.
	$GLOBALS['egMapsDefaultServices']['qp'] = $GLOBALS['egMapsDefaultService'];
	$GLOBALS['egMapsDefaultServices']['fi'] = $GLOBALS['egMapsDefaultService'];
	
	

# Queries

	# Boolean. The default value for the showtitle parameter. Will hide the title in the marker pop-ups when set to true.
	# This value will only be used when the user does not provide one.
	$GLOBALS['smgQPShowTitle'] = true;

	# Boolean. The default value for the hidenamespace parameter. Will hide the namespace in the marker pop-ups when set to true.
	# This value will only be used when the user does not provide one.
	$GLOBALS['smgQPHideNamespace'] = false;
	
	# String or false. Allows you to define the content and it's layout of marker pop-ups via a template.
	# This value will only be used when the user does not provide one.
	$GLOBALS['smgQPTemplate'] = false;
	
	# Enum. The default output format of coordinates.
	# Possible values: Maps_COORDS_FLOAT, Maps_COORDS_DMS, Maps_COORDS_DM, Maps_COORDS_DD
	$GLOBALS['smgQPCoodFormat'] = $GLOBALS['egMapsCoordinateNotation'];
	
	# Boolean. Indicates if coordinates should be outputted in directional notation by default.
	$GLOBALS['smgQPCoodDirectional'] = $GLOBALS['egMapsCoordinateDirectional'];



# Forms

	$GLOBALS['smgFIFieldSize'] = 40;
	
	# Integer or string. The default width and height of maps in forms created by using Semantic Forms.
	# These values only be used when the user does not provide them.
	$GLOBALS['smgFIWidth'] = 665;
	$GLOBALS['smgFIHeight'] = $GLOBALS['egMapsMapHeight'];
	