<?php
/**
 * Root plugin composition and lifecycle hooks.
 *
 * @package Virtual_Llms_Txt
 */

declare( strict_types=1 );

namespace Virtual_Llms_Txt;

/**
 * Registers hooks and handles activation / deactivation.
 */
final class Plugin {

	private static ?self $instance = null;

	/**
	 * Singleton instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	/**
	 * Wire WordPress hooks from the main plugin file.
	 */
	public function register_hooks(): void {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	/**
	 * Load translations and subsystems.
	 */
	public function on_plugins_loaded(): void {
		load_plugin_textdomain(
			'virtual-llms-txt',
			false,
			dirname( plugin_basename( VIRTUAL_LLMS_TXT_PLUGIN_FILE ) ) . '/languages'
		);

		( new Front_Controller() )->register_hooks();

		if ( is_admin() ) {
			( new Admin\Settings_Page() )->register_hooks();
		}
	}

	/**
	 * Fires on plugin activation.
	 */
	public static function activate(): void {
		Options::ensure_defaults();
	}

	/**
	 * Fires on plugin deactivation; optionally removes stored settings.
	 */
	public static function deactivate(): void {
		$stored = Options::get_raw();
		if ( is_array( $stored ) && ! empty( $stored['remove_settings_on_deactivate'] ) ) {
			delete_option( Options::OPTION_KEY );
		}
	}
}
