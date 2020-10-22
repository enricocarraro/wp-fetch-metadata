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

	const HTTP_UNAUTHORIZED = 401;

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
	 * Number of active policies that have been enforced.
	 *
	 * @since 0.0.1
	 *
	 * @var int
	 */
	protected $enforced_policies = 0;

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

		if ( 0 === $this->enforced_policies
		// Browser supports Fetch Metadata.
		&& isset( $headers[ Isolation_Policy::SITE ] ) ) {
			foreach ( $option as $policy_slug => $policy_status_array ) {
				$policy_status = $policy_status_array[0];

				if ( ! isset( $policies[ $policy_slug ] ) || empty( $policy_status ) || Isolation_Policy::STATUS_DISABLED === $policy_status ) {
					continue;
				}

				$policy = $policies[ $policy_slug ];
				if ( ! $policy->is_request_allowed( $headers, $_SERVER ) ) {
					// Since the policy status can either REPORT or ENABLED, log the error.
					error_log( sprintf( 'Isolation policy violation: %s', $policy->error_message( $headers, $_SERVER ) ) );
					if ( Isolation_Policy::STATUS_ENABLED === $policy_status ) {
						// Set Vary header and terminate without fully loading WordPress.
						$this->send_headers();
						wp_die(
						/* translators: %s: policy name. */
							esc_html( sprintf( __( '%s violated.' ), $policy->title ) ),
							esc_html_e( 'Isolation policy violated' ),
							array(
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: self::HTTP_UNAUTHORIZED is considered safe.
								'response' => self::HTTP_UNAUTHORIZED,
								'code'     => 'googlefetchmetadata_isolation_policy_violated',
							)
						);
					}
				}
				$this->enforced_policies++;
			}
		}
	}
	/**
	 * Sets Vary header.
	 *
	 * @since 0.0.1
	 */
	public function send_headers() {
		if ( 0 !== $this->enforced_policies ) {
			header(
				sprintf(
					'%s: %s, %s, %s',
					Isolation_Policy::VARY,
					Isolation_Policy::DEST,
					Isolation_Policy::MODE,
					Isolation_Policy::SITE
				),
				false
			);
		}
	}
}
