<?php
/**
 * Class Google\WP_Fetch_Metadata\Plugin
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata;

use Google\WP_Fetch_Metadata\Policy\Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Resource_Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Navigation_Isolation_Policy;
use Google\WP_Fetch_Metadata\Admin\Settings_Screen;

/**
 * Main class for the plugin.
 *
 * @since 0.0.1
 */
class Plugin {

	/**
	 * Absolute path to the plugin main file.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $main_file;

	/**
	 * Main instance of the plugin.
	 *
	 * @since 0.0.1
	 * @var Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Policy Registry
	 *
	 * @since 0.0.1
	 * @var Policy_Registry
	 */
	protected $policy_registry;

	/**
	 * Policies Setting
	 *
	 * @since 0.0.1
	 * @var Policies_Setting
	 */
	protected $policies_setting;

	/**
	 * Policies
	 *
	 * @since 0.0.1
	 * @var Policies
	 */
	protected $policies;

	/**
	 * Policy Enforcer.
	 *
	 * @since 0.0.1
	 * @var Enforce_Policies
	 */
	protected $enforce_policies;

	/**
	 * Sets the plugin main file.
	 *
	 * @since 0.0.1
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 */
	public function __construct( $main_file ) {
		$this->main_file = $main_file;

		$this->policy_registry  = new Policy_Registry();
		$this->policies         = new Policies( $this->policy_registry );
		$this->policies_setting = new Policies_Setting();
		$this->enforce_policies = new Enforce_Policies( $this->policies, $this->policies_setting );
	}

	/**
	 * Registers the plugin with WordPress.
	 *
	 * @since 0.0.1
	 */
	public function register() {
		add_action(
			'googlefetchmetadata_register_policies',
			function( $policy_registry ) {
				$policy_registry->register( new Default_Resource_Isolation_Policy( 'default-resource-isolation', __( 'Default Resource Isolation Policy', 'fetch-metadata' ) ) );
				$policy_registry->register( new Default_Navigation_Isolation_Policy( 'default-navigation-isolation', __( 'Default Navigation Isolation Policy', 'fetch-metadata' ) ) );
			}
		);

		$this->register_policies();

		add_action(
			'registered_taxonomy',
			array( $this->enforce_policies, 'enforce' )
		);

		add_action(
			'admin_init',
			array( $this->enforce_policies, 'send_headers' )
		);

		add_action(
			'send_headers',
			array( $this->enforce_policies, 'send_headers' )
		);

		$this->policies_setting->register();

		add_action(
			'admin_menu',
			function() {
				$admin_screen = new Admin\Settings_Screen( $this->policies, $this->policies_setting );
				$admin_screen->register_menu();
			}
		);

		add_filter(
			'user_has_cap',
			array( $this, 'grant_fetch_metadata_cap' )
		);
	}

	/**
	 * Instantiates and registers policies.
	 *
	 * @since 0.0.1
	 */
	private function register_policies() {
		/**
		 * Fires when the Plugin class is ready to receive policies.
		 *
		 * The Plugin class stores the policies in the Policy_Registry instance.
		 *
		 * @since n.e.x.t.
		 *
		 * @param Policy_Registry $policy_registry
		 */
		do_action( 'googlefetchmetadata_register_policies', $this->policy_registry );

	}

	/**
	 * Gets the plugin basename, which consists of the plugin directory name and main file name.
	 *
	 * @since 0.0.1
	 *
	 * @return string Plugin basename.
	 */
	public function basename() {
		return plugin_basename( $this->main_file );
	}

	/**
	 * Gets the absolute path for a path relative to the plugin directory.
	 *
	 * @since 0.0.1
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string Absolute path.
	 */
	public function path( $relative_path = '/' ) {
		return plugin_dir_path( $this->main_file ) . ltrim( $relative_path, '/' );
	}

	/**
	 * Gets the full URL for a path relative to the plugin directory.
	 *
	 * @since 0.0.1
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string Full URL.
	 */
	public function url( $relative_path = '/' ) {
		return plugin_dir_url( $this->main_file ) . ltrim( $relative_path, '/' );
	}

	/**
	 * Gets the URL to the plugin's settings screen.
	 *
	 * @since 0.0.1
	 *
	 * @return string Settings screen URL.
	 */
	public function settings_screen_url() {
		return add_query_arg( 'page', Admin\Settings_Screen::SLUG, admin_url( Admin\Settings_Screen::PARENT_SLUG ) );
	}

	/**
	 * Dynamically grants the 'manage_fetch_metadata' capability based on 'manage_options'.
	 *
	 * This method is hooked into the `user_has_cap` filter and can be unhooked and replaced with custom functionality
	 * if needed.
	 *
	 * @since 0.0.1
	 *
	 * @param array $allcaps Associative array of $cap => $grant pairs.
	 * @return array Filtered $allcaps array.
	 */
	public function grant_fetch_metadata_cap( array $allcaps ) {
		if ( isset( $allcaps['manage_options'] ) ) {
			$allcaps[ Admin\Settings_Screen::CAPABILITY ] = $allcaps['manage_options'];
		}

		return $allcaps;
	}

	/**
	 * Retrieves the main instance of the plugin.
	 *
	 * @since 0.0.1
	 *
	 * @return Plugin Plugin main instance.
	 */
	public static function instance() {
		return static::$instance;
	}

	/**
	 * Loads the plugin main instance and initializes it.
	 *
	 * @since 0.0.1
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 * @return bool True if the plugin main instance could be loaded, false otherwise.
	 */
	public static function load( $main_file ) {
		if ( null !== static::$instance ) {
			return false;
		}

		static::$instance = new static( $main_file );
		static::$instance->register();

		return true;
	}
}
