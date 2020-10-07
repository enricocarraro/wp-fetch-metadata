<?php
/**
 * Class Google\WP_Fetch_Metadata\DefaultNavigationIsolationPolicy
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata;

/**
 * Default Navigation Policy
 *
 * @since 0.0.1
 */
class DefaultNavigationIsolationPolicy implements IsolationPolicyInterface {

	public function isRequestAllowed( $headers, $server ) {
		// Disallow cross-site navigation
		if ( $headers[ self::SITE ] === self::SITE_CROSS_SITE
		&& in_array( $headers[ self::MODE ], array( self::MODE_NAVIGATE, self::MODE_NESTED_NAVIGATE ) ) ) {
			return false;
		}

		return true;

	}
}
