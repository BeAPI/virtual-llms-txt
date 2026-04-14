<?php
/**
 * Option keys and defaults for the virtual llms.txt document.
 *
 * @package Virtual_Llms_Txt
 */

declare( strict_types=1 );

namespace Virtual_Llms_Txt;

/**
 * Handles the `virtual_llms_txt` option (per site in multisite).
 */
final class Options {

	public const OPTION_KEY = 'virtual_llms_txt';

	/**
	 * Default option structure.
	 *
	 * @return array<string, mixed>
	 */
	public static function defaults(): array {
		return [
			'content'                       => '',
			'remove_settings_on_deactivate' => false,
		];
	}

	/**
	 * Ensures the option exists with defaults (activation).
	 */
	public static function ensure_defaults(): void {
		$current = get_option( self::OPTION_KEY, false );
		if ( false === $current ) {
			add_option( self::OPTION_KEY, self::defaults() );
		}
	}

	/**
	 * Raw option from the database (may be false or invalid).
	 *
	 * @return array<string, mixed>|false
	 */
	public static function get_raw() {
		return get_option( self::OPTION_KEY, false );
	}

	/**
	 * Merged option array with defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function get(): array {
		$stored = self::get_raw();
		if ( ! is_array( $stored ) ) {
			return self::defaults();
		}
		return array_merge( self::defaults(), $stored );
	}

	/**
	 * Document body for public output.
	 */
	public static function get_content(): string {
		$opts = self::get();
		return isset( $opts['content'] ) && is_string( $opts['content'] ) ? $opts['content'] : '';
	}
}
