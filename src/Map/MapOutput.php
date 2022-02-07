<?php

declare( strict_types = 1 );

namespace Maps\Map;

class MapOutput {

	private string $html;
	private string $headItems;
	private array $resourceModules;

	public function __construct( string $html, array $resourceModules, string $headItems ) {
		$this->html = $html;
		$this->resourceModules = $resourceModules;
		$this->headItems = $headItems;
	}

	public function getHtml(): string {
		return $this->html;
	}

	public function getHeadItems(): string {
		return $this->headItems;
	}

	public function getResourceModules(): array {
		return $this->resourceModules;
	}

	public function addResourcesToParserOutput( \ParserOutput $po ): void {
		if ( $this->headItems !== '' ) {
			$po->addHeadItem( $this->headItems );
		}

		$po->addModules( $this->resourceModules );
	}

}
