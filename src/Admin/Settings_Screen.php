<?php
/**
 * Class Google\WP_Fetch_Metadata\Admin\Settings_Screen
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Admin;

use Google\WP_Fetch_Metadata\Policy\Isolation_Policy;
use Google\WP_Fetch_Metadata\Policies_Setting;

/**
 * Class for the admin settings screen to control policies.
 *
 * @since 0.1.0
 */
class Settings_Screen {

	/**
	 * The admin page slug.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const SLUG = 'fetch_metadata_settings';

	/**
	 * The admin page parent slug.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const PARENT_SLUG = 'options-general.php';

	/**
	 * The capability required to access the admin screen.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const CAPABILITY = 'manage_fetch_metadata';

	/**
	 * Policies controller instance.
	 *
	 * @since 0.1.0
	 * @var Policies
	 */
	protected $policies;

	/**
	 * Policies setting instance.
	 *
	 * @since 0.1.0
	 * @var Policies_Setting
	 */
	protected $policies_setting;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Isolation_Policy[] $policies         List of available policies.
	 * @param Policies_Setting   $policies_setting Policies setting instance.
	 */
	public function __construct( $policies, Policies_Setting $policies_setting ) {
		$this->policies         = $policies;
		$this->policies_setting = $policies_setting;
	}

	/**
	 * Registers the menu item for the admin screen.
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {
		$hook_suffix = add_submenu_page(
			self::PARENT_SLUG,
			__( 'Fetch Metadata Settings', 'fetch-metadata' ),
			__( 'Fetch Metadata', 'fetch-metadata' ),
			self::CAPABILITY,
			self::SLUG,
			array( $this, 'render' )
		);
		add_action(
			"load-{$hook_suffix}",
			function() {
				$this->add_settings_ui();
			}
		);
	}

	/**
	 * Renders the admin screen.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		?>
		<style type="text/css">
			.external-link > .dashicons {
				font-size: 16px;
				text-decoration: none;
			}

			.external-link:hover > .dashicons,
			.external-link:focus > .dashicons {
				text-decoration: none;
			}
		</style>
		<div class="wrap">
			<h1 class="wp-heading-inline">
				<?php esc_html_e( 'Fetch Metadata Settings', 'fetch-metadata' ); ?>
			</h1>
			<hr class="wp-header-end">

			<p>
				<?php esc_html_e( 'Fetch Metadata allows you to control status of the available isolation policies.', 'fetch-metadata' ); ?>
				<?php
				printf(
					'<a class="external-link" href="%1$s" target="_blank">%2$s<span class="screen-reader-text"> %3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>',
					esc_url( _x( 'https://web.dev/fetch-metadata/', 'learn more link', 'fetch-metadata' ) ),
					esc_html__( 'Learn more about Fetch Metadata', 'fetch-metadata' ),
					/* translators: accessibility text */
					esc_html__( '(opens in a new tab)', 'fetch-metadata' )
				);
				?>
			</p>

			<form action="options.php" method="post" novalidate="novalidate">
				<?php settings_fields( self::SLUG ); ?>
				<?php do_settings_sections( self::SLUG ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Adds settings sections and fields.
	 *
	 * @since 0.1.0
	 */
	protected function add_settings_ui() {
		add_settings_section( 'default', '', null, self::SLUG );

		$policies = $this->policies->get_all();
		$option   = $this->policies_setting->get();
		foreach ( $policies as $policy ) {
			add_settings_field(
				$policy->slug,
				$policy->title,
				function( $args ) use ( $policy, $option ) {
					$status = isset( $option[ $policy->slug ] ) ? $option[ $policy->slug ][0] : Isolation_Policy::STATUS_DISABLED;
					$this->render_field( $policy, $status );
				},
				self::SLUG,
				'default',
				array( 'label_for' => $policy->slug )
			);
		}
	}

	/**
	 * Renders the UI field for determining the status of a policy.
	 *
	 * @since 0.1.0
	 *
	 * @param Isolation_Policy $policy Isolation policy.
	 * @param bool             $status  Status set for the isolation policy.
	 */
	protected function render_field( $policy, $status ) {
		$choices = array(
			Isolation_Policy::STATUS_ENABLED  => __( 'Enabled', 'fetch-metadata' ),
			Isolation_Policy::STATUS_DISABLED => __( 'Disabled', 'fetch-metadata' ),
			Isolation_Policy::STATUS_REPORT   => __( 'Report Only', 'fetch-metadata' ),
		);

		?>
		<select id="<?php echo esc_attr( $policy->slug ); ?>" name="<?php echo esc_attr( Policies_Setting::OPTION_NAME . '[' . $policy->slug . '][]' ); ?>">
			<?php
			foreach ( $choices as $value => $label ) {
				?>
				<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $status, $value ); ?>>
					<?php echo esc_html( $label ); ?>
					<?php if ( Isolation_Policy::STATUS_DISABLED === $value ) : ?>
						<?php esc_html_e( '(default)', 'fetch-metadata' ); ?>
					<?php endif; ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}
}
