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

/**
 * Class for enforcing fetch metadata policies.
 *
 * @since 0.0.1
 */
class Enforce_Policies {

	/**
	 * Policies Setting.
	 *
	 * @since 0.0.1
	 * @var Policies_Setting
	 */
	protected $policies_setting;

	/**
	 * Policies.
	 *
	 * @since 0.0.1
	 * @var Policies
	 */
	protected $policies;

	/**
	 * True if the active policies have already been enforced.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	protected static $enforced = false;

	/**
	 * Constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param Policies         $policies All registered policies.
	 * @param Policies_Setting $policies_setting The policies setting instance.
	 */
	public function __construct( Policies $policies, Policies_Setting $policies_setting ) {
		$this->policies         = $policies;
		$this->policies_setting = $policies_setting;
	}

	/**
	 * Enforces active policies.
	 *
	 * @since 0.0.1
	 */
	public function enforce() {
		$policies = $this->policies->get_all();
		$option   = $this->policies_setting->get();
		$headers  = getallheaders();

		if ( ! static::$enforced
		// Browser supports Fetch Metadata.
		&& isset( $headers[ Isolation_Policy::SITE ] ) ) {
			foreach ( $option as $policy_slug => $policy_status_array ) {
				$policy_status = $policy_status_array[0];

				if ( ! isset( $policies[ $policy_slug ] ) ) {
					continue;
				}

				$policy = $policies[ $policy_slug ];

				if ( empty( $policy_status ) || Isolation_Policy::STATUS_DISABLED === $policy_status ) {
					continue;
				}

				if ( Isolation_Policy::STATUS_ENABLED === $policy_status ) {
					if ( ! $policy->is_request_allowed( $headers, $_SERVER ) ) {
						// translators: %s is the policy name.
						wp_die( sprintf( '%s violated.', $policy->title ) );
						// TODO: Add reporting.
					}
				} elseif ( Isolation_Policy::STATUS_REPORT === $policy_status ) {
					// TODO: Add reporting.
					continue;
				}
			}

			static::$enforced = true;
		}

	}
}
