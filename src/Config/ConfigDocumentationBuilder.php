<?php

declare( strict_types = 1 );

namespace Maps\Config;

use MediaWiki\Html\Html;
use MessageLocalizer;

/**
 * Renders the on-page configuration reference for the MediaWiki:Maps config page from the config
 * schema, so it can never drift from the settings that are actually exposed. Per group it renders a
 * table of page key, the value shape accepted there, and the LocalSettings.php setting it overrides.
 * The value shapes come from each type's own describe(); the setting name is the anchor into the
 * external documentation, so no per-setting prose is duplicated here.
 */
class ConfigDocumentationBuilder {

	public const ANCHOR = 'maps-config-reference';

	private const DOCUMENTATION_URL = 'https://maps.extension.wiki/wiki/Configuration';

	public function __construct(
		private ConfigSchema $schema,
		private MessageLocalizer $messageLocalizer
	) {
	}

	/**
	 * A one-line pointer to the on-page reference and the external documentation. Rendered to HTML
	 * so it can be placed directly in the edit form and the view output, neither of which parses
	 * wikitext.
	 */
	public function buildPointer(): string {
		return Html::rawElement(
			'div',
			[ 'class' => 'maps-config-docs-pointer' ],
			$this->messageLocalizer->msg( 'maps-config-docs-pointer', self::ANCHOR, self::DOCUMENTATION_URL )->parse()
		);
	}

	public function buildReference(): string {
		$sections = '';

		foreach ( $this->groupedSettings() as $group => $settings ) {
			$sections .= $this->renderGroup( (string)$group, $settings );
		}

		return Html::rawElement(
			'div',
			[ 'class' => 'maps-config-docs' ],
			Html::element(
				'h2',
				[ 'id' => self::ANCHOR ],
				$this->messageLocalizer->msg( 'maps-config-docs-heading' )->text()
			) . $sections
		);
	}

	/**
	 * @return array<string, ConfigSetting[]> Group name to its settings, in schema order.
	 */
	private function groupedSettings(): array {
		$groups = [];

		foreach ( $this->schema->getSettings() as $setting ) {
			$groups[$setting->group][] = $setting;
		}

		return $groups;
	}

	/**
	 * @param ConfigSetting[] $settings
	 */
	private function renderGroup( string $group, array $settings ): string {
		return Html::rawElement( 'h3', [], Html::element( 'code', [], $group ) )
			. $this->renderTable( $settings );
	}

	/**
	 * @param ConfigSetting[] $settings
	 */
	private function renderTable( array $settings ): string {
		$rows = $this->renderHeaderRow();

		foreach ( $settings as $setting ) {
			$rows .= $this->renderRow( $setting );
		}

		return Html::rawElement( 'table', [ 'class' => 'wikitable' ], $rows );
	}

	private function renderHeaderRow(): string {
		return Html::rawElement(
			'tr',
			[],
			Html::element( 'th', [], $this->messageLocalizer->msg( 'maps-config-docs-column-key' )->text() )
			. Html::element( 'th', [], $this->messageLocalizer->msg( 'maps-config-docs-column-type' )->text() )
			. Html::element( 'th', [], $this->messageLocalizer->msg( 'maps-config-docs-column-setting' )->text() )
		);
	}

	private function renderRow( ConfigSetting $setting ): string {
		return Html::rawElement(
			'tr',
			[],
			Html::rawElement( 'td', [], Html::element( 'code', [], $setting->key ) )
			. Html::element( 'td', [], $this->describeType( $setting->type ) )
			. Html::rawElement( 'td', [], Html::element( 'code', [], '$' . $setting->settingName ) )
		);
	}

	private function describeType( ConfigType $type ): string {
		return $this->messageLocalizer->msg( ...$type->describe() )->text();
	}

}
