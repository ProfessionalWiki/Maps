<?php

/**
 * A class that holds static helper functions for generic mapping-related functions.
 * 
 * @since 0.1
 * 
 * @file Maps_Mapper.php
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
final class MapsMapper {
	
	/**
	 * Determines if a value is a valid map dimension, and optionally corrects it.
	 *
	 * TODO: move to param validation and manipulation classes
	 *
	 * @since 0.6
	 *
	 * @param string or number $value The value as it was entered by the user.
	 * @param string $dimension Must be width or height.
	 * @param boolean $correct If true, the value will be corrected when invalid. Defaults to false.
	 * @param number $default The default value for this dimension. Must be set when $correct = true.
	 *
	 * @return boolean
	 */
	public static function isMapDimension( &$value, $name, array $parameters, $dimension, $correct = false, $default = 0 ) {
		global $egMapsSizeRestrictions;

		// See if the notation is valid.
		if ( !preg_match( '/^\d+(\.\d+)?(px|ex|em|%)?$/', $value ) ) {
			if ( $correct ) {
				$value = $default;
			} else {
				return false;
			}
		}

		// Determine the minimum and maximum values.
		if ( preg_match( '/^.*%$/', $value ) ) {
			if ( count( $egMapsSizeRestrictions[$dimension] ) >= 4 ) {
				$min = $egMapsSizeRestrictions[$dimension][2];
				$max = $egMapsSizeRestrictions[$dimension][3];
			} else {
				// This is for backward compatibility with people who have set a custom min and max before 0.6.
				$min = 1;
				$max = 100;
			}
		} else {
			$min = $egMapsSizeRestrictions[$dimension][0];
			$max = $egMapsSizeRestrictions[$dimension][1];
		}

		// See if the actual value is withing the limits.
		$number = preg_replace( '/[^0-9]/', '', $value );
		if ( $number < $min ) {
			if ( $correct ) {
				$value = $min;
			} else {
				return false;
			}
		} else if ( $number > $max ) {
			if ( $correct ) {
				$value = $max;
			} else {
				return false;
			}
		}

		// If this is a 'correct the value call', add 'px' if no unit has been provided.
		if ( $correct ) {
			if ( !preg_match( '/(px|ex|em|%)$/', $value ) ) {
				$value .= 'px';
			}
		}

		return true;
	}

	/**
	 * Corrects the provided map demension value when not valid.
	 *
	 * @since 0.6
	 *
	 * @param string or number $value The value as it was entered by the user.
	 * @param string $dimension Must be width or height.
	 * @param number $default The default value for this dimension.
	 */
	public static function setMapDimension( &$value, $name, array $parameters, $dimension, $default ) {
		self::isMapDimension( $value, $name, $parameters, $dimension, true, $default );
	}
	
	/**
	 * Add a JavaScript file out of skins/common, or a given relative path.
	 * 
	 * This is a copy of the native function in OutputPage to work around a pre MW 1.16 bug.
	 * Should be used for adding external files, like the Google Maps API.
	 * 
	 * @param OutputPage $out
	 * @param string $file
	 */
	public static function addScriptFile( OutputPage $out, $file ) {
		global $wgStylePath, $wgStyleVersion;
		if( substr( $file, 0, 1 ) == '/' || preg_match( '#^[a-z]*://#i', $file ) ) {
			$path = $file;
		} else {
			$path =  "{$wgStylePath}/common/{$file}";
		}
		$out->addScript( Html::linkedScript( wfAppendQuery( $path, $wgStyleVersion ) ) );		
	}
	
	/**
	 * Adds a string of JavaScript as dependency for a mapping service
	 * after wrapping it in an onload hook and script tag. This is sort
	 * of a hack, but it takes care of the difference between artciles
	 * and special pages.
	 * 
	 * @since 0.7
	 * 
	 * @param iMappingService $service 
	 * @param string $script
	 */
	public static function addInlineScript( iMappingService $service, $script ) {
		static $addOnloadJs = false;
		
		$hasRL = method_exists( 'ParserOutput', 'addModules' );
		
		if ( $hasRL && !$addOnloadJs ) {
			global $egMapsScriptPath, $egMapsStyleVersion;
			
			$service->addDependency(
				Html::linkedScript( "$egMapsScriptPath/includes/mapsonload.js?$egMapsStyleVersion" )
			);
			
			$addOnloadJs = true;
		} 		
		
		$service->addDependency( Html::inlineScript( 
			( $hasRL ? 'addMapsOnloadHook' : 'addOnloadHook' ) . "( function() { $script } );"
		) );
	}
	
	/**
	 * This function returns the definitions for the parameters used by every map feature.
	 *
	 * @return array
	 */
	public static function getCommonParameters() {
		global $egMapsAvailableServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService, $egMapsMapWidth, $egMapsMapHeight, $egMapsDefaultService;

		$params = array();
		
		$params['mappingservice'] = new Parameter( 'mappingservice' );
		$params['mappingservice']->addAliases( 'service' );
		$params['mappingservice']->setDefault( $egMapsDefaultService );
		$params['mappingservice']->addCriteria( new CriterionInArray( MapsMappingServices::getAllServiceValues() ) );
		
		$params['geoservice'] = new Parameter(
			'geoservice', 
			Parameter::TYPE_STRING,
			$egMapsDefaultGeoService,
			array(),
			array(
				new CriterionInArray( $egMapsAvailableGeoServices ),
			),
			array( 'mappingservice' )
		);
		
		$params['zoom'] = new Parameter(
			'zoom', 
			Parameter::TYPE_INTEGER
		);
		
		$params['width'] = new Parameter(
			'width', 
			Parameter::TYPE_STRING,
			$egMapsMapWidth,
			array(),
			array(
				new CriterionMapDimension( 'width' ),
			)
		);
		$params['width']->addManipulations( new MapsParamDimension( 'width' ) );

		$params['height'] = new Parameter(
			'height', 
			Parameter::TYPE_STRING,
			$egMapsMapHeight,
			array(),
			array(
				new CriterionMapDimension( 'height' ),
			)
		);
		$params['height']->addManipulations( new MapsParamDimension( 'height' ) );
		
		return $params;
	}
	
	/**
	 * Resolves the url of images provided as wiki page; leaves others alone.
	 * 
	 * @since 0.7.1
	 * 
	 * @param string $image
	 * 
	 * @return string
	 */
	public static function getImageUrl( $image ) {
		$title = Title::newFromText( $image, NS_FILE );
		
		if ( $title->getNamespace() == NS_FILE && $title->exists() ) {
			$imagePage = new ImagePage( $title );
			$image = $imagePage->getDisplayedFile()->getURL();
		}		
		
		return $image;
	}
	
}