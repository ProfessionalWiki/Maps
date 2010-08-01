<?php

/**
 * File holding the MapsMappingService class.
 *
 * @file Maps_MappingService.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Base class for mapping services. Deriving classes hold mapping service specific 
 * information and functionality, which can be used by any mapping feature.
 * 
 * @since 0.6.3
 * 
 * @author Jeroen De Dauw
 */
abstract class MapsMappingService implements iMappingService {
	
	/**
	 * The internal name of the service.
	 * 
	 * @since 0.6.3
	 * 
	 * @var string
	 */
	protected $serviceName;
	
	/**
	 * A list of aliases for the internal name.
	 * 
	 * @since 0.6.3
	 * 
	 * @var array
	 */
	protected $aliases;
	
	/**
	 * A list of features that support the service, used for validation and defaulting.
	 * 
	 * @since 0.6.3
	 * 
	 * @var array
	 */
	protected $features;
	
	/**
	 * A list of parameter info specific to the service, which can be used by any feature
	 * to pass along to Validator to handle parameters.
	 * 
	 * @since 0.6.3
	 * 
	 * @var mixed Array or false
	 */
	private $parameterInfo = false;
	
	/**
	 * A list of dependencies (header items) that have been added.
	 * 
	 * @since 0.6.3
	 * 
	 * @var array
	 */
	private $addedDependencies = array();
	
	/**
	 * A list of dependencies (header items) that need to be added.
	 * 
	 * @since 0.6.3
	 * 
	 * @var array
	 */
	private $dependencies = array();
	
	/**
	 * Constructor. Creates a new instance of MapsMappingService.
	 * 
	 * @since 0.6.3
	 * 
	 * @param string $serviceName
	 * @param array $aliases
	 */
	function __construct( $serviceName, array $aliases = array() ) {
		$this->serviceName = $serviceName;
		$this->aliases = $aliases;
	}
	
	/**
	 * @see iMappingService::getParameterInfo
	 * 
	 * @since 0.6.3
	 */	
	public final function getParameterInfo() {
		if ( $this->parameterInfo === false ) {
			$this->parameterInfo = array();
			$this->initParameterInfo( $this->parameterInfo );
		}
		
		return $this->parameterInfo;
	}
	
	/**
	 * @see iMappingService::createMarkersJs
	 * 
	 * @since 0.6.5
	 */
	public function createMarkersJs( array $markers ) {
		return '[]';
	}		
	
	/**
	 * @see iMappingService::addFeature
	 * 
	 * @since 0.6.3
	 */
	public function addFeature( $featureName, $handlingClass ) {
		$this->features[$featureName] = $handlingClass;
	}
	
	/**
	 * @see iMappingService::addDependencies 
	 * 
	 * @since 0.6.3
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
	 * @see iMappingService::getDependencyHtml 
	 * 
	 * @since 0.6.3
	 */
	public final function getDependencyHtml() {
		$allDependencies = array_merge( $this->getDependencies(), $this->dependencies );
		$dependencies = array();
		
		// Only add dependnecies that have not yet been added.
		foreach ( $allDependencies as $dependency ) {
			if ( !in_array( $dependency, $this->addedDependencies ) ) {
				$dependencies[] = $dependency;
				$this->addedDependencies[] = $dependency;
			}
		}
		
		// If there are dependencies, put them all together in a string, otherwise return false.
		return count( $dependencies ) > 0 ? implode( '', $dependencies ) : false;
	}
	
	/**
	 * @see iMappingService::addDependency
	 * 
	 * @since 0.6.3
	 */
	public final function addDependency( $dependencyHtml ) {
		$this->dependencies[] = $dependencyHtml;
	}	
	
	/**
	 * @see iMappingService::getName
	 * 
	 * @since 0.6.3
	 */	
	public function getName() {
		return $this->serviceName;
	}
	
	/**
	 * @see iMappingService::getFeature
	 * 
	 * @since 0.6.3
	 */
	public function getFeature( $featureName ) {
		return array_key_exists( $featureName, $this->features ) ? $this->features[$featureName] : false;
	}
	
	/**
	 * @see iMappingService::getFeatureInstance
	 * 
	 * @since 0.6.6
	 */
	public function getFeatureInstance( $featureName ) {
		$className = $this->getFeature( $featureName );
		
		if ( $className === false || !class_exists( $className ) ) {
			// TODO: log/throw error, as this should not happen
		}
		
		return new $className( $this );
	}	
	
	/**
	 * @see iMappingService::getAliases
	 * 
	 * @since 0.6.3
	 */
	public function getAliases() {
		return $this->aliases;
	}
	
	/**
	 * @see iMappingService::hasAlias
	 * 
	 * @since 0.6.3
	 */
	public function hasAlias( $alias ) {
		return in_array( $alias, $this->aliases );
	}
	
	/**
	 * Returns a list of html fragments, such as script includes, the current service depends on.
	 * 
	 * @since 0.6.3
	 * 
	 * @return array
	 */
	protected function getDependencies() {
		return array();
	}
	
	/**
	 * Initializes the service parameters.
	 * 
	 * You can override this method to set service specific parameters in the inheriting class. 
	 * 
	 * @since 0.6.3
	 * 
	 * @param array $parameters
	 */	
	protected function initParameterInfo( array &$parameters ) {
	}	
	
}