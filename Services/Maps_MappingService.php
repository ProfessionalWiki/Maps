<?php

/**
 * This file holds the general information for the Google Maps service
 *
 * @file Maps_MappingService.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsMappingService implements iMappingService {
	
	/**
	 * 
	 * @var string
	 */
	protected $mServiceName;
	
	/**
	 * 
	 * @var array
	 */
	protected $mAliases;
	
	/**
	 * 
	 * @var array
	 */
	protected $mFeatures;
	
	/**
	 * 
	 * @var mixed Array or false
	 */
	private $mParameterInfo = false;
	
	/**
	 * 
	 * @var array
	 */
	private $mAddedDependencies = array();
	
	private $mDependencies = array();
	
	/**
	 * Constructor. Creates a new instance of MapsMappingService.
	 * 
	 * @param string $serviceName
	 * @param array $aliases
	 */
	function __construct( $serviceName, array $aliases = array() ) {
		$this->mServiceName = $serviceName;
		$this->mAliases = $aliases;
	}
	
	/**
	 * Returns the service parameters by first checking if they have been initialized yet,
	 * doing to work if this is not the case, and then returning them.
	 * 
	 * @return array
	 */	
	public final function getParameterInfo() {
		if ( $this->mParameterInfo === false ) {
			$this->mParameterInfo = array();
			$this->initParameterInfo( $this->mParameterInfo );
		}
		
		return $this->mParameterInfo;
	}
	
	/**
	 * Initializes the service parameters.
	 * 
	 * You can override this method to set service specific parameters in the inheriting class. 
	 */	
	protected function initParameterInfo( array &$parameters ) {
	}
	
	/**
	 * Adds a feature to this service. This is to indicate this service has support for this feature.
	 * 
	 * @param string $featureName
	 * @param string $handlingClass
	 */
	public function addFeature( $featureName, $handlingClass ) {
		$this->mFeatures[$featureName] = $handlingClass;
	}
	
	/**
	 * Adds the mapping services dependencies to the header. 
	 * 
	 * @param mixed $parserOrOut
	 */
	public final function addDependencies( &$parserOrOut ) {
		$dependencies = $this->getDependencyHtml();
		
		// Only aff a head item when there are dependencies.
		if ( $dependencies ) {
			if ( $parserOrOut instanceof Parser ) {
				$parserOrOut->getOutput()->addHeadItem( $dependencies );
			} 
			else if ( $parserOrOut instanceof OutputPage ) { 
				$parserOrOut->addHeadItem( md5( $dependencies ), $dependencies );
			}			
		}
	}
	
	/**
	 * Returns the html for the needed dependencies or false.
	 * 
	 * @return mixed Steing or false
	 */
	public final function getDependencyHtml() {
		$allDependencies = array_merge( $this->getDependencies(), $this->mDependencies );
		$dependencies = array();
		
		// Only add dependnecies that have not yet been added.
		foreach ( $allDependencies as $dependency ) {
			if ( !in_array( $dependency, $this->mAddedDependencies ) ) {
				$dependencies[] = $dependency;
				$this->mAddedDependencies[] = $dependency;
			}
		}
		
		// If there are dependencies, put them all together in a string, otherwise return false.
		return count( $dependencies ) > 0 ? implode( '', $dependencies ) : false;
	}
	
	/**
	 * Adds a dependency that is needed for this service. It will be passed along with the next 
	 * call to getDependencyHtml or addDependencies.
	 * 
	 * @param string $dependencyHtml
	 */
	public final function addDependency( $dependencyHtml ) {
		$this->mDependencies[] = $dependencyHtml;
	}	
	
	/**
	 * Returns a list of html fragments, such as script includes, the current service depends on.
	 * 
	 * @return array
	 */
	protected function getDependencies() {
		return array();
	}
	
	/**
	 * Returns the internal name of the service.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->mServiceName;
	}
	
	/**
	 * Returns the name of the class that handles the provided feature in this service, or false if there is none.
	 * 
	 * @param string $featureName.
	 * 
	 * @return mixed String or false
	 */
	public function getFeature( $featureName ) {
		return array_key_exists( $featureName, $this->mFeatures ) ? $this->mFeatures[$featureName] : false;
	}
	
	/**
	 * Returns a list of aliases.
	 * 
	 * @return array
	 */
	public function getAliases() {
		return $this->mAliases;
	}
	
	/**
	 * Returns if the service has a certain alias or not.
	 * 
	 * @param string $alias
	 * 
	 * @return boolean
	 */
	public function hasAlias( $alias ) {
		return in_array( $alias, $this->mAliases );
	}
	
}