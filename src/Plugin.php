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
	 * Main instance of the plugin.
	 *
	 * @since 0.0.1
	 * @var IsolationPolicyInterface|null
	 */
	protected $policy = null;

	/**
	 * Sets the plugin main file.
	 *
	 * @since 0.0.1
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 */
	public function __construct( $main_file ) {
		$this->main_file = $main_file;
		$this->policy = new DefaultResourceIsolationPolicy();
	}

	/**
	 * Registers the plugin with WordPress.
	 *
	 * @since 0.0.1
	 */
	public function register() {
		/*
		add_action('muplugins_loaded', function  () {
			if( ! isset( $GLOBALS['fetch-metadata-policy'] ) ) {
				$GLOBALS['fetch-metadata-policy'] = true;
				$headers = getallheaders();
				if(isset($headers['Sec-Fetch-Site'])) {
					$GLOBALS['IT-WORKS'] = true;
				} else {
					$GLOBALS['IT-WORKS'] = false;//printf('browser does not support fetch metadata');
				}
			}

		});*/
		add_action(
			'registered_taxonomy',
			function () {
				if ( ! $this->fetched ) {
					$this->fetched = true;

					$headers       = getallheaders();
					$this->enabled = $headers['Sec-Fetch-Site'] . ' ' . $headers['Sec-Fetch-Mode'] . ' ' . $headers['Sec-Fetch-Dest'];
					if ( ! isset( $headers['Sec-Fetch-Site'] ) ) {
						return;
					}

					if ( in_array( $headers['Sec-Fetch-Site'], array( 'same-origin', 'same-site', 'none' ) ) ) {
						return;
					}

					if (
					$headers['Sec-Fetch-Mode'] === 'navigate' && $_SERVER['REQUEST_METHOD'] === 'GET'
					&& ( ! in_array( $headers['Sec-Fetch-Dest'], array( 'object', 'embed' ) ) )
					) {
						return;
					}

					wp_die( 'Resource Isolation Policy violated' );
				}
			}
		);

		add_action(
			'wp_footer',
			function () {
				if ( $this->fetched ) {
					// var_dump($this->method);
					if ( $this->enabled ) {
						printf( 'it works %s', $this->enabled );
					} else {
						printf( 'fetch metadata is not supported' );
					}
					printf( 'anythin' );
				} else {
					printf( ' not set' );
				}
			}
		);

		add_action(
			'admin_footer',
			function () {
				if ( $this->fetched ) {
					// var_dump($this->method);

					if ( $this->enabled ) {
						printf( 'it works' );
					} else {
						printf( 'fetch metadata is not supported' );
					}
					printf( 'anythin' );
				} else {
					printf( ' not set' );
				}
			}
		);
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
