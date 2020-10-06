<?php

namespace Google\WP_Fetch_Metadata;

class DefaultResourceIsolationPolicy implements IsolationPolicyInterface {

	public function isRequestAllowed( $headers, $server ) {
		if ( ! isset( $headers[ self::SEC_FETCH_SITE_HEADER ] ) ) {
			return true;
		}

		if ( in_array( $headers[ self::SEC_FETCH_SITE_HEADER ], array( self::SAME_ORIGIN, self::SAME_SITE, self::NONE ) ) ) {
			return true;
		}

		if (
			$headers[ self::SEC_FETCH_MODE_HEADER ] === self::MODE_NAVIGATE
			&& $server['REQUEST_METHOD'] === 'GET'
			&& ( ! in_array( $headers[ self::SEC_FETCH_DEST_HEADER ], array( self::DEST_OBJECT, self::DEST_EMBED ) ) )
		) {
			return true;
		}

		return false;
	}
}
