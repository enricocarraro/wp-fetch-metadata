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

use Google\WP_Fetch_Metadata\Policy\Isolation_Policy;

/**
 * Class for registering third party policies.
 *
 * @since 0.0.1
 * @access private
 * @ignore
 */
class Policy_Registry {

	/**
	 * The list of active policies.
	 *
	 * @since 0.0.1
	 * @var Isolation_Policy[]
	 */
	private $policies;

	/**
	 * Policy_Registry constructor.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->policies = array();
	}

	/**
	 * Registers a policies.
	 *
	 * @since 0.0.1
	 *
	 * @param Isolation_Policy $policy The policy to be registered.
	 */
	public function register( Isolation_Policy $policy ) {
		$this->policies[] = $policy;
	}

	/**
	 * Gets the list of active policies.
	 *
	 * @since 0.0.1
	 *
	 * @return Isolation_Policy[] The list of active policies.
	 */
	public function get_all() {
		return $this->policies;
	}
}
