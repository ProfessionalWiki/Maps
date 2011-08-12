<?php

/**
 * Static class for interaction with the settings of the Maps extension.
 * 
 * @since 1.1
 * 
 * @file Maps_Settings.php
 * @ingroup Maps
 * 
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsSettings extends ExtensionSettings {
	
	protected static function initIfNeeded() {
		parent::initIfNeeded();
		
		self::$settings['php-bc'] = self::getPhpCompatSettings();
	}
	
	/**
	 * Returns a name => value array with the default settings.
	 * 
	 * @since 1.1
	 * 
	 * @return array
	 */
	protected static function getDefaultSettings() {
		static $defaultSettings = false;

		if ( $defaultSettings === false ) {
			$defaultSettings = include 'Maps.settings.php'; 
		}
		
		return $defaultSettings;
	}

	/**
	 * Returns a name => value array with the default settings
	 * specified using global PHP variables.
	 * 
	 * @since 1.1
	 * 
	 * @return array
	 */
	protected static function getPhpSettings() {
		return $GLOBALS['egMapsSettings'];
	}
	
	/**
	 * Returns a name => value array with the default settings
	 * specified using global PHP variables that have been deprecated.
	 * 
	 * @since 1.1
	 * 
	 * @return array
	 */
	protected static function getPhpCompatSettings() {
		$mappings = array(
			'egMapsFoobar' => 'foobar' // TODO
		);
		
		$settings = array();
		
		foreach ( $mappings as $oldName => $newName ) {
			if ( array_key_exists( $oldName, $GLOBALS ) ) {
				$settings[$newName] = $GLOBALS[$oldName];
			}
		}
		
		return $settings;
	}
	
}

abstract class ExtensionSettings {
	
	protected static $settings = false;
	
	protected static $mergedCaches = array();
	
	/**
	 * Returns a name => value array with the default settings.
	 * 
	 * @since 1.1
	 * 
	 * @return array
	 */
	protected abstract static function getDefaultSettings();
	
	protected static function initIfNeeded() {
		if ( self::$settings === false ) {
			self::$settings = array(
				'default' => self::getDefaultSettings(),
				'php' => self::getPhpSettings()
			);
		}
	}
	
	protected static function getMergedSettings( array $groups, $cache = true ) {
		$names = implode( '|', $groups ); 
		
		if ( array_key_exists( $names, self::$mergedCaches )  ) {
			return self::$mergedCaches[$names];
		}
		else {
			$settings = array();
			
			foreach ( $groups as $group ) {
				if ( array_key_exists( $group, self::$settings ) ) {
					$settings = array_merge_recursive( $settings, self::$settings[$group] );
				}
			}
			
			if ( $cache ) {
				self::$mergedCaches[$names] = $settings;
			}
			
			return $settings;
		}
	}
	
	/**
	 * Returns a name => value array with the default settings
	 * specified using global PHP variables.
	 * 
	 * @since 1.1
	 * 
	 * @return array
	 */
	protected static function getPhpSettings() {
		return array();
	}
	
	/**
	 * Returns all settings for a group.
	 * 
	 * @since 1.1
	 * 
	 * @param array|boolean $groups True to use all overrides, false for none, array for custom set or order. 
	 * @param boolean $cache Cache the merging of groups or not?
	 * 
	 * @return array
	 */
	public static function getSettings( $groups = true, $cache = true ) {
		self::initIfNeeded();
		
		if ( $groups === false ) {
			return self::getDefaultSettings(); 
		}
		else {
			if ( $groups === true ) {
				$groups = array_keys( self::$settings );
			}
			return self::getMergedSettings( $groups, $cache );
		}
	}
	
	/**
	 * Returns the value of a single setting.
	 * 
	 * @since 1.1
	 * 
	 * @param string $settingName
	 * @param array|boolean $groups
	 * @param boolean $cache Cache the merging of groups or not?
	 * 
	 * @return mixed
	 */
	public static function get( $settingName, $groups = true, $cache = true ) {
		$settings = self::getSettings( $groups, $cache );
		
		if ( !array_key_exists( $settingName, $settings ) ) {
			throw new MWException(); // TODO
		}
		
		return $settings[$settingName];
	}

	/**
	 * Returns if a single setting exists or not.
	 * 
	 * @since 1.1
	 * 
	 * @param string $settingName
	 * @param array|boolean $groups
	 * @param boolean $cache Cache the merging of groups or not?
	 * 
	 * @return boolean
	 */
	public static function has( $settingName, $groups = true, $cache = true ) {
		$settings = self::getSettings( $groups, $cache );
		return array_key_exists( $settingName, $settings );
	}

	/**
	 * Set a sigle setting in the specified group.
	 * 
	 * @since 1.1
	 * 
	 * @param string $settingName
	 * @param mixed $settingValue
	 * @param string $groupName
	 * @param boolean $invalidateCache
	 * 
	 * @return boolean
	 */
	public static function set( $settingName, $settingValue, $groupName, $invalidateCache = true ) {
		if ( !array_key_exists( $groupName, self::$settings ) ) {
			self::$settings[$groupName] = array();
		}
		elseif ( $invalidateCache
			&& ( !array_key_exists( $settingName, self::$settings[$groupName] )
				|| self::$settings[$groupName][$settingName] !== $settingValue ) ) {
			foreach ( array_keys( self::$mergedCaches ) as $cacheName ) {
				if ( in_array( $groupName, explode( '|', $cacheName ) ) ) {
					unset( self::$mergedCaches[$cacheName] );
				}
			}
		}
		
		self::$settings[$groupName][$settingName] = $settingValue;
	}
	
}
