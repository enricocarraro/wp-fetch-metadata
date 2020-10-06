<?php

namespace Google\WP_Fetch_Metadata;

interface IsolationPolicyInterface {
	const SEC_FETCH_SITE_HEADER = 'sec-fetch-site';
	const SEC_FETCH_MODE_HEADER = 'sec-fetch-mode';
	const SEC_FETCH_DEST_HEADER = 'sec-fetch-dest';
	const CORS                  = 'cors';
	const CROSS_SITE            = 'cross-site';
	const SAME_ORIGIN           = 'same-origin';
	const SAME_SITE             = 'same-site';
	const NONE                  = 'none';
	const NO_CORS               = 'no-cors';
	const MODE_NAVIGATE         = 'navigate';
	const MODE_WEBSOCKET        = 'websocket';
	const MODE_NESTED_NAVIGATE  = 'nested-navigate';
	const DEST_AUDIO            = 'audio';
	const DEST_AUDIOWORKLET     = 'audioworklet';
	const DEST_DOCUMENT         = 'document';
	const DEST_EMBED            = 'embed';
	const DEST_EMPTY            = 'empty';
	const DEST_FONT             = 'font';
	const DEST_IMAGE            = 'image';
	const DEST_MANIFEST         = 'manifest';
	const DEST_OBJECT           = 'object';
	const DEST_PAINTWORKLET     = 'paintworklet';
	const DEST_REPORT           = 'report';
	const DEST_SCRIPT           = 'script';
	const DEST_SERVICEWORKER    = 'serviceworker';
	const DEST_SHAREDWORKER     = 'sharedworker';
	const DEST_STYLE            = 'style';
	const DEST_TRACK            = 'track';
	const DEST_VIDEO            = 'video';
	const DEST_WORKER           = 'worker';
	const DEST_XSLT             = 'xslt';
	const DEST_NESTED_DOCUMENT  = 'nested-document';
	const VARY_HEADER           = 'Vary';

	/**
	 * Is request allowed?
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the policy allows the current request, false otherwise.
	 */
	public function isRequestAllowed( $headers, $server);

}
