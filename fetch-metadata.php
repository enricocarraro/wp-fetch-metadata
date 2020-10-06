<?php
/**
 * Plugin initialization file
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 *
 * @wordpress-plugin
 * Plugin Name: Fetch Metadata
 * Plugin URI:  https://wordpress.org/plugins/fetch-metadata
 * Description: WordPress plugin to create Resource Isolation Policies based on Fetch Metadata Request Headers.
 * Version:     0.0.1
 * Author:      Google
 * Author URI:  https://opensource.google.com
 * License:     Apache License 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: fetch-metadata
 */

/* This file must be parseable by PHP 5.2. */

/**
 * Loads the plugin.
 *
 * @since 0.0.1
 */
function wp_fetch_metadata_load() {
	if ( version_compare( phpversion(), '5.6', '<' ) ) {
		add_action( 'admin_notices', 'wp_fetch_metadata_display_php_version_notice' );
		return;
	}

	if ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
		add_action( 'admin_notices', 'wp_fetch_metadata_display_wp_version_notice' );
		return;
	}

	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	call_user_func( array( 'Google\\WP_Fetch_Metadata\\Plugin', 'load' ), __FILE__ );
}

/**
 * Displays an admin notice about an unmet PHP version requirement.
 *
 * @since 0.0.1
 */
function wp_fetch_metadata_display_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			sprintf(
				/* translators: 1: required version, 2: currently used version */
				__( 'Fetch Metadata requires at least PHP version %1$s. Your site is currently running on PHP %2$s.', 'fetch-metadata' ),
				'5.6',
				phpversion()
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Displays an admin notice about an unmet WordPress version requirement.
 *
 * @since 0.0.1
 */
function wp_fetch_metadata_display_wp_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			sprintf(
				/* translators: 1: required version, 2: currently used version */
				__( 'Fetch Metadata requires at least WordPress version %1$s. Your site is currently running on WordPress %2$s.', 'fetch-metadata' ),
				'5.0',
				get_bloginfo( 'version' )
			);
			?>
		</p>
	</div>
	<?php
}

wp_fetch_metadata_load();
