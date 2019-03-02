<?php

namespace Maps;

use ParamProcessor\ParamDefinition;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class MappingService {

	/**
	 * @return array[]|ParamDefinition[]
	 */
	public abstract function getParameterInfo(): array;

	public abstract function getDependencyHtml( array $params ): string;

	/**
	 * Returns the resource modules that need to be loaded to use this mapping service.
	 */
	public abstract function getResourceModules(): array;

	public abstract function getName(): string;

	public abstract function getAliases(): array;

	public abstract function hasAlias( string $alias ): bool;

	public abstract function getMapId();

}
