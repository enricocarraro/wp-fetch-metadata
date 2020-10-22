<?php
/**
 * Class Google\WP_Fetch_Metadata\Policy\Isolation_Policy
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Policy;

/**
 * Isolation policy abstract class.
 *
 * @since 0.0.1
 */
abstract class Isolation_Policy {
	const DEST                 = 'Sec-Fetch-Dest';
	const DEST_AUDIO           = 'audio';
	const DEST_AUDIOWORKLET    = 'audioworklet';
	const DEST_DOCUMENT        = 'document';
	const DEST_EMBED           = 'embed';
	const DEST_EMPTY           = 'empty';
	const DEST_FONT            = 'font';
	const DEST_IMAGE           = 'image';
	const DEST_MANIFEST        = 'manifest';
	const DEST_OBJECT          = 'object';
	const DEST_PAINTWORKLET    = 'paintworklet';
	const DEST_REPORT          = 'report';
	const DEST_SCRIPT          = 'script';
	const DEST_SERVICEWORKER   = 'serviceworker';
	const DEST_SHAREDWORKER    = 'sharedworker';
	const DEST_STYLE           = 'style';
	const DEST_TRACK           = 'track';
	const DEST_VIDEO           = 'video';
	const DEST_WORKER          = 'worker';
	const DEST_XSLT            = 'xslt';
	const DEST_NESTED_DOCUMENT = 'nested-document';

	const MODE                 = 'Sec-Fetch-Mode';
	const MODE_NAVIGATE        = 'navigate';
	const MODE_NESTED_NAVIGATE = 'nested-navigate';
	const MODE_CORS            = 'cors';
	const MODE_NO_CORS         = 'no-cors';
	const MODE_WEBSOCKET       = 'websocket';

	const SITE             = 'Sec-Fetch-Site';
	const SITE_CROSS_SITE  = 'cross-site';
	const SITE_NONE        = 'none';
	const SITE_SAME_ORIGIN = 'same-origin';
	const SITE_SAME_SITE   = 'same-site';

	const USER = 'Sec-Fetch-User';

	const VARY = 'Vary';

	const STATUS_ENABLED  = 'enabled';
	const STATUS_DISABLED = 'disabled';
	const STATUS_REPORT   = 'reporting';

	/**
	 * Policy slug.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Policy title.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param string $slug Policy slug.
	 * @param string $title Policy title.
	 */
	public function __construct( $slug, $title ) {
		$this->slug  = $slug;
		$this->title = $title;
	}

	/**
	 * Checks if the current request can be allowed.
	 *
	 * @since 0.0.1
	 *
	 * @param string[string] $headers Request headers.
	 * @param string[string] $server $_SERVER super-global variable.
	 *
	 * @return bool
	 */
	abstract public function is_request_allowed( $headers, $server);

	/**
	 * Policy violation error message.
	 *
	 * @since 0.0.1
	 *
	 * @param string[string] $headers Request headers.
	 * @param string[string] $server $_SERVER super-global variable.
	 *
	 * @return string Error message.
	 */
	public function error_message( $headers, $server ) {
		// Doesn't need translation since it is only used for logging.
		return sprintf(
			'%1$s violated for %2$s request to %3$s: %4$s=%5$s, %6$s=%7$s, %8$s=%9$s.',
			$this->title,
			$server['REQUEST_METHOD'],
			$server['REQUEST_URI'],
			self::DEST,
			$headers[ self::DEST ],
			self::MODE,
			$headers[ self::MODE ],
			self::SITE,
			$headers[ self::SITE ]
		);
	}

	/**
	 * Magic getter.
	 *
	 * @since 0.0.1
	 *
	 * @param string $prop Property to get.
	 * @return mixed The property value, or null if property not set.
	 */
	public function __get( $prop ) {
		if ( 'title' === $prop || 'slug' === $prop ) {
			return $this->{$prop};
		}

		return null;
	}

}
