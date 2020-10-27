<?php
/**
 * Class Google\WP_Fetch_Metadata\Tests\PHPUnit\Integration\Enforce_Policies_Tests
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Tests\PHPUnit\Integration;

use Google\WP_Fetch_Metadata\Tests\PHPUnit\Framework\Integration_Test_Case;
use Google\WP_Fetch_Metadata\Policy\Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Navigation_Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Resource_Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy_Registry;
use Google\WP_Fetch_Metadata\Enforce_Policies;
use Google\WP_Fetch_Metadata\Policies;
use Google\WP_Fetch_Metadata\Policies_Setting;
use Brain\Monkey\Functions;

/**
 * Class containing a Enforce_Policies Tests.
 */
class Enforce_Policies_Tests extends Integration_Test_Case {

	const DEST = 'HTTP_SEC_FETCH_DEST';
	const SITE = 'HTTP_SEC_FETCH_SITE';
	const MODE = 'HTTP_SEC_FETCH_MODE';

	/**
	 * Sets up the environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		add_filter( 'wp_die_handler', 'wp_die_handler_filter' );
	}

	/**
	 * Tears down the environment after each test.
	 */
	public function tearDown() {
		parent::tearDown();
		remove_filter( 'wp_die_handler', 'wp_die_handler_filter' );
	}

	/**
	 * Tests enforce() when all default policies are enabled and respected.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceDefaultPoliciesEnabledRespected() {
		$_SERVER = array(
			self::SITE       => Isolation_Policy::SITE_SAME_SITE,
			self::MODE       => Isolation_Policy::MODE_NAVIGATE,
			self::DEST       => Isolation_Policy::DEST_DOCUMENT,
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/',
		);

		$navigation = new Default_Navigation_Isolation_Policy( 'default-navigation', 'Default Navigation Isolation Policy' );
		$resource   = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

		$setting = $this->createMock( Policies_Setting::class );
		$setting
			->method( 'get' )
			->willReturn(
				array(
					$navigation->slug => array( Isolation_Policy::STATUS_ENABLED ),
					$resource->slug   => array( Isolation_Policy::STATUS_ENABLED ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $navigation );
		$policy_registry->register( $resource );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		$this->assertSame( '', $error );
	}

	/**
	 * Tests enforce() when all default policies are enabled and the navigation policy is violated.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceDefaultPoliciesEnabledViolated() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_CROSS_SITE,
				self::MODE       => Isolation_Policy::MODE_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/',
			);

			$navigation = new Default_Navigation_Isolation_Policy( 'default-navigation', 'Default Navigation Isolation Policy' );
			$resource   = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$navigation->slug => array( Isolation_Policy::STATUS_ENABLED ),
					$resource->slug   => array( Isolation_Policy::STATUS_ENABLED ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $navigation );
		$policy_registry->register( $resource );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}
		$this->assertSame( $navigation->title . ' violated.', $error );
	}

	/**
	 * Tests enforce() when the default navigation isolation policy is enabled and respected.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceNavigationPolicyEnabledRespected() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_SAME_ORIGIN,
				self::MODE       => Isolation_Policy::MODE_NESTED_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/',
			);

			$navigation = new Default_Navigation_Isolation_Policy( 'default-navigation', 'Default Navigation Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$navigation->slug => array( Isolation_Policy::STATUS_ENABLED ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $navigation );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}
		$this->assertSame( '', $error );
	}

	/**
	 * Tests enforce() when the default navigation isolation policy is enabled and violated.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceNavigationPolicyEnabledViolated() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_CROSS_SITE,
				self::MODE       => Isolation_Policy::MODE_NESTED_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/',
			);

			$navigation = new Default_Navigation_Isolation_Policy( 'default-navigation', 'Default Navigation Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$navigation->slug => array( Isolation_Policy::STATUS_ENABLED ),
				)
			);
			$policy_registry = new Policy_Registry();
			$policies        = new Policies( $policy_registry );
			$enforcer        = new Enforce_Policies( $policies, $setting );
			$policy_registry->register( $navigation );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}
		$this->assertSame( $navigation->title . ' violated.', $error );
	}

	/**
	 * Tests enforce() when the default resource isolation policy is enabled and respected.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceResourcePolicyEnabledRespected() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_CROSS_SITE,
				self::MODE       => Isolation_Policy::MODE_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/',
			);

			$resource = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$resource->slug => array( Isolation_Policy::STATUS_ENABLED ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $resource );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		$this->assertSame( '', $error );
	}

	/**
	 * Tests enforce() when the default resource isolation policy is enabled and violated.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceResourcePolicyEnabledViolated() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_CROSS_SITE,
				self::MODE       => Isolation_Policy::MODE_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/',
			);

			$resource = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$resource->slug => array( Isolation_Policy::STATUS_ENABLED ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $resource );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		$this->assertSame( $resource->title . ' violated.', $error );
	}

	/**
	 * Tests enforce() when the default resource isolation policy is in report mode and respected.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceResourcePolicyReportRespected() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_CROSS_SITE,
				self::MODE       => Isolation_Policy::MODE_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/',
			);

			$resource = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$resource->slug => array( Isolation_Policy::STATUS_REPORT ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $resource );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		$this->assertSame( '', $error );
	}

	/**
	 * Tests enforce() when the default resource isolation policy is in report mode and violated.
	 *
	 * @since 0.0.1
	 */
	public function testEnforceResourcePolicyReportViolated() {
			$_SERVER = array(
				self::SITE       => Isolation_Policy::SITE_CROSS_SITE,
				self::MODE       => Isolation_Policy::MODE_NAVIGATE,
				self::DEST       => Isolation_Policy::DEST_DOCUMENT,
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/',
			);

			$resource = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

			$setting = $this->createMock( Policies_Setting::class );
			$setting
			->method( 'get' )
			->willReturn(
				array(
					$resource->slug => array( Isolation_Policy::STATUS_REPORT ),
				)
			);
		$policy_registry = new Policy_Registry();
		$policies        = new Policies( $policy_registry );
		$enforcer        = new Enforce_Policies( $policies, $setting );

		$policy_registry->register( $resource );

		try {
			$enforcer->enforce();
			$error = '';
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		$this->assertSame( '', $error );
	}
}
