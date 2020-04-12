<?php

declare( strict_types = 1 );

namespace Maps\Map;

use Maps\MappingService;

class MapOutputBuilder {

	public function buildOutput( MappingService $service, MapData $mapData ): MapOutput {
		return new MapOutput(
			$this->buildMapHtml( $service, $mapData ),
			$service->getResourceModules( $mapData->getParameters() ),
			$service->getDependencyHtml( $mapData->getParameters() )
		);
	}

	private function buildMapHtml( MappingService $service, MapData $mapData ): string {
		$htmlBuilder = new MapHtmlBuilder();

		return $htmlBuilder->getMapHTML(
			( new MapDataSerializer() )->toJson( $mapData ),
			$service->newMapId(),
			$service->getName()
		);
	}

}
