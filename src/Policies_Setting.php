<?php
/**
 * Class Google\WP_Fetch_Metadata\Policy_Registry
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata;

/**
 * Class representing the policies setting.
 *
 * @since 0.0.1
 */
class Policies_Setting {

	const OPTION_NAME = 'fetch_metadata_policies';

	/**
	 * Registers the admin screen with WordPress.
	 *
	 * @since 0.0.1
	 */
	public function register() {
		add_action(
			'init',
			function() {
				register_setting(
					Admin\Settings_Screen::SLUG,
					self::OPTION_NAME,
					array(
						'type'              => 'object',
						'description'       => __( 'Active Fetch Metadata policies.', 'fetch-metadata' ),
						'sanitize_callback' => array( $this, 'sanitize' ),
						'default'           => array(),
					)
				);
			}
		);
	}

	/**
	 * Gets the policies list from the option.
	 *
	 * @since 0.0.1
	 *
	 * @return array Associative array of $policy_name => $active pairs.
	 */
	public function get() {
		return array_filter( (array) get_option( self::OPTION_NAME, array() ) );
	}

	/**
	 * Sanitizes the value for the setting.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Unsanitized setting value.
	 * @return array Associative array of $policy_name => $policy_origins pairs.
	 */
	public function sanitize( $value ) {
		// TODO: This is probably too basic.
		if ( ! is_array( $value ) ) {
			return array();
		}
		return $value;
	}
}
