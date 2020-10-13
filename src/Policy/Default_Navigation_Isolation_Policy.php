<?php
/**
 * Class Google\WP_Fetch_Metadata\Policy\Default_Navigation_Isolation_Policy
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Policy;

/**
 * Default navigation isolation policy that implements {@see Isolation_Policy}.
 *
 * @see Isolation_Policy
 *
 * @since 0.0.1
 */
class Default_Navigation_Isolation_Policy extends Isolation_Policy {

	/**
	 * Checks if the current request can be allowed.
	 *
	 * @since 0.0.1
	 *
	 * @param string[string] $headers Request headers.
	 * @param string[string] $server $_SERVER super-global variable.
	 */
	public function is_request_allowed( $headers, $server ) {
		// Disallow cross-site navigation.
		if ( self::SITE_CROSS_SITE === $headers[ self::SITE ]
		&& in_array( $headers[ self::MODE ], array( self::MODE_NAVIGATE, self::MODE_NESTED_NAVIGATE ), true ) ) {

			return false;
		}

		return true;
	}
}
