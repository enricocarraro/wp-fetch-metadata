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
 * Class for controlling policies.
 *
 * @since 0.1.0
 */
class Policies {

	/**
	 * Internal storage for lazy-loaded policies, also to prevent double initialization.
	 *
	 * @since 0.1.0
	 * @var array
	 */
	protected $policies = array();

	/**
	 * Policy Registry
	 *
	 * @since 0.1.0
	 * @var Policy_Registry
	 */
	protected $policy_registry;

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @param Policy_Registry $policy_registry Policy Registry instance.
	 */
	public function __construct( Policy_Registry $policy_registry ) {
		$this->policy_registry = $policy_registry;
	}

	/**
	 * Gets all the available policies.
	 *
	 * @since 0.1.0
	 *
	 * @return Isolation_Policy[string] Associative array of $policy_slug => $policy_instance pairs.
	 */
	public function get_all() {
		if ( ! empty( $this->policies ) ) {
			return $this->policies;
		}

		$policies = $this->policy_registry->get_all();

		$this->policies = array();
		foreach ( $policies as $policy ) {
			$this->policies[ $policy->slug ] = $policy;
		}

		return $this->policies;
	}
}
