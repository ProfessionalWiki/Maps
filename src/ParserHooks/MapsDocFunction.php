<?php

declare( strict_types = 1 );

namespace Maps\ParserHooks;

use Maps\MapsFactory;
use ParamProcessor\ParamDefinition;
use ParamProcessor\ProcessingResult;
use Parser;
use ParserHook;
use ParserHooks\HookDefinition;
use ParserHooks\HookHandler;

/**
 * Class for the 'mapsdoc' parser hooks,
 * which displays documentation for a specified mapping service.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDocFunction implements HookHandler {

	private string $language;

	public function handle( Parser $parser, ProcessingResult $result ) {
		foreach ( $result->getErrors() as $error ) {
			if ( $error->isFatal() ) {
				return '<div><span class="errorbox">' .
					wfMessage( 'validator-fatal-error', $error->getMessage() )->parse() .
					'</span></div><br /><br />';
			}
		}

		$parameters = $result->getParameters();

		$this->language = $parameters['language']->getValue();

		$factory = MapsFactory::globalInstance();

		return $this->getParameterTable(
			$factory,
			$this->getServiceParameters( $factory, $parameters['service']->getValue() )
		);
	}

	private function getServiceParameters( MapsFactory $factory, string $service ) {
		return array_merge(
			[
				'zoom' => [
					'type' => 'integer',
					'message' => 'maps-par-zoom',
				]
			],
			$factory->getMappingServices()->getService( $service )->getParameterInfo()
		);
	}

	/**
	 * Returns the wikitext for a table listing the provided parameters.
	 */
	private function getParameterTable( MapsFactory $factory, array $parameters ): string {
		$tableRows = [];

		$parameters = $factory->getParamDefinitionFactory()->newDefinitionsFromArrays( $parameters );

		foreach ( $parameters as $parameter ) {
			$tableRows[] = $this->getDescriptionRow( $parameter );
		}

		$table = '';

		if ( count( $tableRows ) > 0 ) {
			$tableRows = array_merge(
				[
					'!' . $this->msg( 'validator-describe-header-parameter' ) . "\n" .
					//'!' . $this->msg( 'validator-describe-header-aliases' ) ."\n" .
					'!' . $this->msg( 'validator-describe-header-type' ) . "\n" .
					'!' . $this->msg( 'validator-describe-header-default' ) . "\n" .
					'!' . $this->msg( 'validator-describe-header-description' )
				],
				$tableRows
			);

			$table = implode( "\n|-\n", $tableRows );

			$table =
				'{| class="wikitable sortable"' . "\n" .
				$table .
				"\n|}";
		}

		return $table;
	}

	/**
	 * Returns the wikitext for a table row describing a single parameter.
	 *
	 * @param ParamDefinition $parameter
	 *
	 * @return string
	 */
	private function getDescriptionRow( ParamDefinition $parameter ) {
		$description = $this->msg( $parameter->getMessage() );

		$type = $this->msg( $parameter->getTypeMessage() );

		$default = $parameter->isRequired() ? "''" . $this->msg(
				'validator-describe-required'
			) . "''" : $parameter->getDefault();
		if ( is_array( $default ) ) {
			$default = implode( ', ', $default );
		} elseif ( is_bool( $default ) ) {
			$default = $default ? 'yes' : 'no';
		}

		if ( $default === '' ) {
			$default = "''" . $this->msg( 'validator-describe-empty' ) . "''";
		}

		return <<<EOT
| {$parameter->getName()}
| {$type}
| {$default}
| {$description}
EOT;
	}

	/**
	 * Message function that takes into account the language parameter.
	 *
	 * @param string $key
	 * @param ... $args
	 *
	 * @return string
	 */
	private function msg() {
		$args = func_get_args();
		$key = array_shift( $args );
		return wfMessage( $key, $args )->inLanguage( $this->language )->text();
	}

	/**
	 * @see ParserHook::getDescription()
	 */
	public function getMessage() {
		return 'maps-mapsdoc-description';
	}

	public static function getHookDefinition(): HookDefinition {
		return new HookDefinition(
			'mapsdoc',
			self::getParameterInfo(),
			[ 'service', 'language' ]
		);
	}

	private static function getParameterInfo(): array {
		$params = [];

		$params['service'] = [
			'values' => $GLOBALS['egMapsAvailableServices'],
			'tolower' => true,
		];

		$params['language'] = [
			'default' => $GLOBALS['wgLanguageCode'],
		];

		// Give grep a chance to find the usages:
		// maps-geocode-par-service, maps-geocode-par-language
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-geocode-par-' . $name;
		}

		return $params;
	}

}
