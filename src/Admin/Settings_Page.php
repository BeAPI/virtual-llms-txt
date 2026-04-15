<?php
/**
 * Settings screen under Settings in wp-admin.
 *
 * @package Virtual_Llms_Txt
 */

declare( strict_types=1 );

namespace Virtual_Llms_Txt\Admin;

use Virtual_Llms_Txt\Options;

/**
 * Registers the options page and setting.
 */
final class Settings_Page {

	private const PAGE_SLUG    = 'virtual-llms-txt-settings';
	private const OPTION_GROUP = 'virtual_llms_txt_settings';

	/**
	 * Register admin hooks.
	 */
	public function register_hooks(): void {
		add_action( 'admin_init', [ $this, 'register_setting' ] );
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_notices', [ $this, 'maybe_warn_plain_permalinks' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( VIRTUAL_LLMS_TXT_PLUGIN_FILE ), [ $this, 'add_action_links' ] );
	}

	/**
	 * Register the option with sanitization.
	 */
	public function register_setting(): void {
		register_setting(
			self::OPTION_GROUP,
			Options::OPTION_KEY,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_options' ],
				'default'           => Options::defaults(),
			]
		);
	}

	/**
	 * Add submenu under Settings.
	 */
	public function register_menu(): void {
		add_options_page(
			__( 'Virtual llms.txt', 'virtual-llms-txt' ),
			__( 'Virtual llms.txt', 'virtual-llms-txt' ),
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Warn administrators on this plugin's settings screen when plain permalinks prevent /llms.txt from working.
	 */
	public function maybe_warn_plain_permalinks(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( null === $screen || 'settings_page_' . self::PAGE_SLUG !== $screen->id ) {
			return;
		}

		if ( '' !== (string) get_option( 'permalink_structure' ) ) {
			return;
		}

		$permalink_url = admin_url( 'options-permalink.php' );
		?>
		<div class="notice notice-warning">
			<p>
				<strong><?php esc_html_e( 'Virtual llms.txt', 'virtual-llms-txt' ); ?>:</strong>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: URL to Permalink Settings screen. */
						__( 'Pretty permalinks are required: with the Plain structure, the virtual <code>/llms.txt</code> URL cannot be served and this plugin will not work. Choose any other permalink structure under %s.', 'virtual-llms-txt' ),
						sprintf(
							'<a href="%s">%s</a>',
							esc_url( $permalink_url ),
							esc_html__( 'Permalink Settings', 'virtual-llms-txt' )
						)
					),
					[
						'code' => [],
						'a'    => [
							'href' => true,
						],
					]
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Sanitize stored option array on save.
	 *
	 * @param mixed $value Submitted value.
	 * @return array<string, mixed>
	 */
	public function sanitize_options( $value ): array {
		$defaults = Options::defaults();
		$previous = Options::get();

		if ( ! is_array( $value ) ) {
			return $previous;
		}

		$content = isset( $value['content'] ) ? (string) wp_unslash( $value['content'] ) : $previous['content'];
		$content = sanitize_textarea_field( $content );

		$remove = ! empty( $value['remove_settings_on_deactivate'] );

		return [
			'content'                       => $content,
			'remove_settings_on_deactivate' => (bool) $remove,
		];
	}

	/**
	 * Output the settings form.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options   = Options::get();
		$preview   = home_url( '/llms.txt' );
		$textarea  = isset( $options['content'] ) ? (string) $options['content'] : '';
		$remove_on = ! empty( $options['remove_settings_on_deactivate'] );

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>
				<strong><?php esc_html_e( 'Public URL', 'virtual-llms-txt' ); ?>:</strong>
				<a href="<?php echo esc_url( $preview ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $preview ); ?></a>
			</p>
			<p class="description">
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: linked label "llms.txt". */
						__( 'For document structure, see the %s overview site.', 'virtual-llms-txt' ),
						sprintf(
							'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
							esc_url( 'https://llmstxt.org/' ),
							esc_html__( 'llms.txt', 'virtual-llms-txt' )
						)
					),
					[
						'a' => [
							'href'   => true,
							'target' => true,
							'rel'    => true,
						],
					]
				);
				?>
			</p>
			<form action="options.php" method="post">
				<?php settings_fields( self::OPTION_GROUP ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="virtual-llms-txt-content"><?php esc_html_e( 'Document body', 'virtual-llms-txt' ); ?></label>
						</th>
						<td>
							<textarea class="large-text code" rows="18" cols="50" id="virtual-llms-txt-content" name="<?php echo esc_attr( Options::OPTION_KEY ); ?>[content]"><?php echo esc_textarea( $textarea ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Plain text (often Markdown-style). This is served as text/plain without HTML escaping.', 'virtual-llms-txt' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Deactivation', 'virtual-llms-txt' ); ?></th>
						<td>
							<input type="hidden" name="<?php echo esc_attr( Options::OPTION_KEY ); ?>[remove_settings_on_deactivate]" value="0" />
							<label for="virtual-llms-txt-remove">
								<input type="checkbox" id="virtual-llms-txt-remove" name="<?php echo esc_attr( Options::OPTION_KEY ); ?>[remove_settings_on_deactivate]" value="1" <?php checked( $remove_on ); ?> />
								<?php esc_html_e( 'Delete all plugin settings when this plugin is deactivated.', 'virtual-llms-txt' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Add a shortcut link on the Plugins list screen.
	 *
	 * @param array<int, string> $links Existing links.
	 * @return array<int, string>
	 */
	public function add_action_links( array $links ): array {
		$url = admin_url( 'options-general.php?page=' . self::PAGE_SLUG );
		array_unshift(
			$links,
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				esc_html__( 'Settings', 'virtual-llms-txt' )
			)
		);
		return $links;
	}
}
