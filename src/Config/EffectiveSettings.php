<?php

declare( strict_types = 1 );

namespace Maps\Config;

use Throwable;

/**
 * The effective Maps settings: the PHP settings overlaid with the values set on the MediaWiki:Maps
 * config page, per the config schema. A wiki value replaces the PHP value, except for the layer
 * definition and availability maps, which are merged per name.
 *
 * The wiki page is read lazily on first use and the overlay is memoized for the request, so it is
 * only read at parse time, never during extension setup. When wiki config is disabled, or the page
 * is missing or unreadable, the PHP settings are used unchanged. A wiki value that does not pass its
 * type's validation is ignored, so a value that bypassed save-time validation cannot take effect.
 */
class EffectiveSettings {

	private ?array $overlay = null;

	/**
	 * @param array<string, mixed> $phpSettings The PHP settings, keyed by setting name.
	 */
	public function __construct(
		private array $phpSettings,
		private ConfigSchema $schema,
		private WikiConfigSource $wikiConfigSource,
		private bool $wikiConfigEnabled
	) {
	}

	public function get( string $settingName ): mixed {
		$overlay = $this->getOverlay();

		if ( array_key_exists( $settingName, $overlay ) ) {
			return $overlay[$settingName];
		}

		return $this->phpSettings[$settingName] ?? null;
	}

	private function getOverlay(): array {
		$this->overlay ??= $this->buildOverlay();

		return $this->overlay;
	}

	private function buildOverlay(): array {
		if ( !$this->wikiConfigEnabled ) {
			return [];
		}

		try {
			$wikiConfig = $this->wikiConfigSource->getConfig();
		} catch ( Throwable $e ) {
			return [];
		}

		return $wikiConfig === null ? [] : $this->overlayFrom( $wikiConfig );
	}

	private function overlayFrom( array $wikiConfig ): array {
		$overlay = [];

		foreach ( $this->schema->getSettings() as $setting ) {
			$group = $wikiConfig[$setting->group] ?? null;

			if ( is_array( $group ) && array_key_exists( $setting->key, $group ) ) {
				$this->overlaySetting( $overlay, $setting, $group[$setting->key] );
			}
		}

		return $overlay;
	}

	private function overlaySetting( array &$overlay, ConfigSetting $setting, mixed $value ): void {
		if ( $setting->type->validate( $value, $setting->group . '.' . $setting->key ) === [] ) {
			$overlay[$setting->settingName] = $setting->mergeStrategy->combine(
				$this->phpSettings[$setting->settingName] ?? null,
				$value
			);
		}
	}

}
