<?php
/**
 * Plugin bootstrap for Virtual llms.txt.
 *
 * @package Virtual_Llms_Txt
 *
 * Plugin Name:       Virtual llms.txt
 * Plugin URI:        https://llmstxt.org/
 * Description:       Serves a virtual llms.txt document from site settings (plain text, per site).
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       virtual-llms-txt
 * Domain Path:       languages
 *
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace Virtual_Llms_Txt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VIRTUAL_LLMS_TXT_VERSION', '1.0.0' );
define( 'VIRTUAL_LLMS_TXT_PLUGIN_FILE', __FILE__ );
define( 'VIRTUAL_LLMS_TXT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * PSR-4 autoload for Virtual_Llms_Txt\* classes under src/.
 *
 * @param string $class_name Fully qualified class name.
 */
function autoload( string $class_name ): void {
	$prefix = 'Virtual_Llms_Txt\\';
	if ( ! str_starts_with( $class_name, $prefix ) ) {
		return;
	}
	$relative = substr( $class_name, strlen( $prefix ) );
	$relative = str_replace( '\\', DIRECTORY_SEPARATOR, $relative );
	$file     = VIRTUAL_LLMS_TXT_PLUGIN_DIR . 'src' . DIRECTORY_SEPARATOR . $relative . '.php';
	if ( is_readable( $file ) ) {
		require_once $file;
	}
}

spl_autoload_register( __NAMESPACE__ . '\\autoload' );

register_activation_hook( VIRTUAL_LLMS_TXT_PLUGIN_FILE, [ Plugin::class, 'activate' ] );
register_deactivation_hook( VIRTUAL_LLMS_TXT_PLUGIN_FILE, [ Plugin::class, 'deactivate' ] );

Plugin::instance()->register_hooks();
