<?php

namespace Maps;

use ParamProcessor\ParamDefinition;
use ParserOutput;

/**
 * @deprecated
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class MappingService {

	/**
	 * A list of dependencies (header items) that have been added.
	 *
	 * @var array
	 */
	private $addedDependencies = [];

	/**
	 * A list of dependencies (header items) that need to be added.
	 *
	 * @var array
	 */
	private $dependencies = [];

	/**
	 * @return array[]|ParamDefinition[]
	 */
	public abstract function getParameterInfo(): array;

	/**
	 * @since 5.2.0
	 *
	 * @param ParserOutput $parserOutput
	 */
	public final function addDependencies( ParserOutput $parserOutput ) {
		$dependencies = $this->getDependencyHtml();

		// Only add a head item when there are dependencies.
		if ( $dependencies ) {
			$parserOutput->addHeadItem( $dependencies );
		}

		$parserOutput->addModules( $this->getResourceModules() );
	}

	/**
	 * @since 0.6.3
	 */
	public final function getDependencyHtml() {
		$allDependencies = array_merge( $this->getDependencies(), $this->dependencies );
		$dependencies = [];

		// Only add dependencies that have not yet been added.
		foreach ( $allDependencies as $dependency ) {
			if ( !in_array( $dependency, $this->addedDependencies ) ) {
				$dependencies[] = $dependency;
				$this->addedDependencies[] = $dependency;
			}
		}

		// If there are dependencies, put them all together in a string, otherwise return false.
		return $dependencies !== [] ? implode( '', $dependencies ) : false;
	}

	/**
	 * Returns a list of html fragments, such as script includes, the current service depends on.
	 *
	 * @since 0.6.3
	 *
	 * @return array
	 */
	protected function getDependencies() {
		return [];
	}

	/**
	 * Returns the resource modules that need to be loaded to use this mapping service.
	 */
	public abstract function getResourceModules(): array;

	public abstract function getName(): string;

	public abstract function getAliases(): array;

	public abstract function hasAlias( string $alias ): bool;

	/**
	 * @param array $dependencies
	 */
	public function addHtmlDependencies( array $dependencies ) {
		foreach ( $dependencies as $dependency ) {
			$this->addHtmlDependency( $dependency );
		}
	}

	/**
	 * @since 0.6.3
	 *
	 * @param $dependencyHtml
	 */
	public final function addHtmlDependency( $dependencyHtml ) {
		$this->dependencies[] = $dependencyHtml;
	}

	public abstract function getMapId();

}
