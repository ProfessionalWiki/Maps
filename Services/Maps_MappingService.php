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
	 * Returns the service parameters by first checking if they have been initialized yet,
	 * doing to work if this is not the case, and then returning them.
	 * 
	 * @since 0.6.3
	 * 
	 * @see iMappingService::getParameterInfo
	 * 
	 * @return array
	 */	
	public final function getParameterInfo() {
		if ( $this->parameterInfo === false ) {
			$this->parameterInfo = array();
			$this->initParameterInfo( $this->parameterInfo );
		}
		
		return $this->parameterInfo;
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
	
	/**
	 * @see iMappingService::createMarkersJs
	 * 
	 * @since 0.6.5
	 */
	public function createMarkersJs( array $markers ) {
		return '[]';
	}		
	
	/**
	 * Adds a feature to this service. This is to indicate this service has support for this feature.
	 * 
	 * @since 0.6.3
	 * 
	 * @param string $featureName
	 * @param string $handlingClass
	 */
	public function addFeature( $featureName, $handlingClass ) {
		$this->features[$featureName] = $handlingClass;
	}
	
	/**
	 * Adds the mapping services dependencies to the header. 
	 * 
	 * @since 0.6.3
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
	 * @since 0.6.3
	 * 
	 * @return mixed String or false
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
	 * Adds a dependency that is needed for this service. It will be passed along with the next 
	 * call to getDependencyHtml or addDependencies.
	 * 
	 * @since 0.6.3
	 * 
	 * @param string $dependencyHtml
	 */
	public final function addDependency( $dependencyHtml ) {
		$this->dependencies[] = $dependencyHtml;
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
	 * Returns the internal name of the service.
	 * 
	 * @since 0.6.3
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->serviceName;
	}
	
	/**
	 * Returns the name of the class that handles the provided feature in this service, or false if there is none.
	 * 
	 * @since 0.6.3
	 * 
	 * @param string $featureName.
	 * 
	 * @return mixed String or false
	 */
	public function getFeature( $featureName ) {
		return array_key_exists( $featureName, $this->features ) ? $this->features[$featureName] : false;
	}
	
	/**
	 * Returns a list of aliases.
	 * 
	 * @since 0.6.3
	 * 
	 * @return array
	 */
	public function getAliases() {
		return $this->aliases;
	}
	
	/**
	 * Returns if the service has a certain alias or not.
	 * 
	 * @since 0.6.3
	 * 
	 * @param string $alias
	 * 
	 * @return boolean
	 */
	public function hasAlias( $alias ) {
		return in_array( $alias, $this->aliases );
	}
	
}