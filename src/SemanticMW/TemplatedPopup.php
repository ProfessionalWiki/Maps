<?php

declare( strict_types = 1 );

namespace Maps\SemanticMW;

use DataValues\Geo\Values\LatLongValue;
use Parser;

class TemplatedPopup {

	private $parser;
	private $templateName;
	private $extraParameter;

	public function __construct( Parser $parser, string $templateName, string $extraParameter ) {
		$this->parser = $parser;
		$this->templateName = $templateName;
		$this->extraParameter = $extraParameter;
	}

	public function getHtml( string $title, LatLongValue $latLong, array $properties ): string {
		$segments = array_merge(
			[
				$this->templateName,
				'title=' . $title,
				'latitude=' . $latLong->getLatitude(),
				'longitude=' . $latLong->getLongitude(),
				'userparam=' . $this->extraParameter
			],
			$properties
		);

		return $this->parser->recursiveTagParseFully(
			'{{' . implode( '|', $segments ) . '}}'
		);
	}

}
