<?php
/**
 * Class Google\WP_Fetch_Metadata\DefaultResourceIsolationPolicy
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata;

class DefaultResourceIsolationPolicy implements IsolationPolicyInterface {

	public function isRequestAllowed( $headers, $server ) {
		// Allow same-site, same-origin and direct navigation
		if ( in_array( $headers[ self::SITE ], array( self::SITE_SAME_ORIGIN, self::SITE_SAME_SITE, self::SITE_NONE ) ) ) {
			return true;
		}

		// Allow simple top-level navigation and iframing
		if (
			$headers[ self::MODE ] === self::MODE_NAVIGATE
			&& $server['REQUEST_METHOD'] === 'GET'
			&& ( ! in_array( $headers[ self::DEST ], array( self::DEST_OBJECT, self::DEST_EMBED ) ) )
		) {
			return true;
		}

		return false;
	}
}
