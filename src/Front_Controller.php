<?php
/**
 * Public HTTP response for /llms.txt.
 *
 * @package Virtual_Llms_Txt
 */

declare( strict_types=1 );

namespace Virtual_Llms_Txt;

/**
 * Serves the virtual document on GET and HEAD requests.
 */
final class Front_Controller {

	/**
	 * Register front-end hooks.
	 */
	public function register_hooks(): void {
		add_action( 'template_redirect', [ $this, 'maybe_serve_llms_txt' ], 0 );
	}

	/**
	 * Output plain text when the request targets this site's llms.txt URL.
	 */
	public function maybe_serve_llms_txt(): void {
		if ( ! $this->is_llms_txt_request() ) {
			return;
		}

		$method = $this->get_request_method();
		if ( 'GET' !== $method && 'HEAD' !== $method ) {
			return;
		}

		$content = Options::get_content();

		status_header( 200 );
		nocache_headers();
		header( 'Content-Type: text/plain; charset=UTF-8' );

		if ( 'HEAD' === $method ) {
			exit;
		}

		// Intentionally unescaped: served as a plain-text document, not HTML.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
		exit;
	}

	/**
	 * Whether the current request path matches the canonical llms.txt path.
	 */
	private function is_llms_txt_request(): bool {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$request_uri  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );
		if ( ! is_string( $request_path ) ) {
			return false;
		}

		$llms_url  = home_url( '/llms.txt' );
		$llms_path = wp_parse_url( $llms_url, PHP_URL_PATH );
		if ( ! is_string( $llms_path ) ) {
			return false;
		}

		$request_path = untrailingslashit( wp_normalize_path( $request_path ) );
		$llms_path    = untrailingslashit( wp_normalize_path( $llms_path ) );

		return $request_path === $llms_path;
	}

	/**
	 * Normalized HTTP method.
	 */
	private function get_request_method(): string {
		if ( empty( $_SERVER['REQUEST_METHOD'] ) ) {
			return '';
		}
		return strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) );
	}
}
