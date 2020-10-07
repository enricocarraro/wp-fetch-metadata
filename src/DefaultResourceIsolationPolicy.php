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

/**
 * Default resource isolation policy that implements {@see IsolationPolicyInterface}.
 * This policy is based on {@link https://web.dev/fetch-metadata/ web.dev/fetch-metadata/}.
 *
 * @see IsolationPolicyInterface
 *
 * @link https://web.dev/fetch-metadata/ web.dev/fetch-metadata/
 *
 * @since 0.0.1
 */
class DefaultResourceIsolationPolicy implements IsolationPolicyInterface {
	/**
	 * Checks if the current request can be allowed.
	 *
	 * @since 0.0.1
	 *
	 * @param array[string]string $headers Request headers.
	 * @param array[string]string $server $_SERVER super-global variable.
	 */
	public function is_request_allowed( $headers, $server ) {
		// Allow same-site, same-origin and direct navigation.
		if ( in_array( $headers[ self::SITE ], array( self::SITE_SAME_ORIGIN, self::SITE_SAME_SITE, self::SITE_NONE ), true ) ) {
			return true;
		}

		// Allow simple top-level navigation and iframing.
		if (
			self::MODE_NAVIGATE === $headers[ self::MODE ]
			&& 'GET' === $server['REQUEST_METHOD']
			&& ( ! in_array( $headers[ self::DEST ], array( self::DEST_OBJECT, self::DEST_EMBED ), true ) )
		) {
			return true;
		}

		return false;
	}
}
