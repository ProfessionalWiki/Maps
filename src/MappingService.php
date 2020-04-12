<?php

declare( strict_types = 1 );

namespace Maps;

use Maps\Map\MapData;
use ParamProcessor\ParamDefinition;
use ParamProcessor\ProcessingResult;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface MappingService {

	public function getName(): string;

	public function getAliases(): array;

	/**
	 * @return array[]|ParamDefinition[]
	 */
	public function getParameterInfo(): array;

	public function getDependencyHtml( array $params ): string;

	/**
	 * Returns the resource modules that need to be loaded to use this mapping service.
	 */
	public function getResourceModules( array $params ): array;

	public function newMapId(): string;

	public function newMapDataFromProcessingResult( ProcessingResult $processingResult ): MapData;

	public function newMapDataFromParameters( array $params ): MapData;

}
