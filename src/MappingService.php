<?php

namespace Maps;

use ParamProcessor\ParamDefinition;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface MappingService {

	public function getName(): string;

	public function getAliases(): array;

	public function hasAlias( string $alias ): bool;

	/**
	 * @return array[]|ParamDefinition[]
	 */
	public function getParameterInfo(): array;

	public function getDependencyHtml( array $params ): string;

	/**
	 * Returns the resource modules that need to be loaded to use this mapping service.
	 */
	public function getResourceModules(): array;

	public function newMapId();

}
