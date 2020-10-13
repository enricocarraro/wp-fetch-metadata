<?php
/**
 * Class Google\WP_Fetch_Metadata\Tests\PHPUnit\Unit\Policy\Default_Isolation_Policies
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Tests\PHPUnit\Unit\Policy;

use Google\WP_Fetch_Metadata\Tests\PHPUnit\Framework\Unit_Test_Case;
use Google\WP_Fetch_Metadata\Policy\Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Navigation_Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Resource_Isolation_Policy;

/**
 * Class that tests the Default Isolation Policies.
 */
class Default_Isolation_Policies extends Unit_Test_Case {

	/**
	 * Stores instance of Default_Navigation_Isolation_Policy class.
	 *
	 * @since 0.0.1
	 *
	 * @var Default_Navigation_Isolation_Policy
	 */
	protected $navigation;

	/**
	 * Stores instance of Default_Resource_Isolation_Policy class.
	 *
	 * @since 0.0.1
	 *
	 * @var Default_Resource_Isolation_Policy
	 */
	protected $resource;

	/**
	 * Instantiates Default_Navigation_Isolation_Policy and Default_Resource_Isolation_Policy.
	 *
	 * @since 0.0.1
	 */
	protected function setUp() {
		$this->navigation = new Default_Navigation_Isolation_Policy( 'default-navigation', 'Default Navigation Isolation Policy' );
		$this->resource   = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );
	}


	/**
	 * Tests allowed request headers against the Default Navigation Isolation Policy.
	 *
	 * @since 0.0.1
	 */
	public function testNavigationAllowed() {
		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_SAME_ORIGIN,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$this->assertTrue( $this->navigation->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_SAME_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NESTED_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_EMBED,
		);
		$this->assertTrue( $this->navigation->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_NONE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_CORS,
			Isolation_Policy::DEST => Isolation_Policy::DEST_SCRIPT,
		);
		$this->assertTrue( $this->navigation->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NO_CORS,
			Isolation_Policy::DEST => Isolation_Policy::DEST_SCRIPT,
		);
		$this->assertTrue( $this->navigation->is_request_allowed( $headers, null ) );
	}

	/**
	 * Tests diallowed request headers against the Default Navigation Isolation Policy.
	 *
	 * @since 0.0.1
	 */
	public function testNavigationDisallowed() {

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$this->assertFalse( $this->navigation->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NESTED_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$this->assertFalse( $this->navigation->is_request_allowed( $headers, null ) );
	}

	/**
	 * Tests allowed request headers against the Default Resource Isolation Policy.
	 *
	 * @since 0.0.1
	 */
	public function testResourceAllowed() {
		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_SAME_ORIGIN,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$this->assertTrue( $this->resource->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_SAME_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NESTED_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$this->assertTrue( $this->resource->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_NONE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NESTED_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$this->assertTrue( $this->resource->is_request_allowed( $headers, null ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$server  = array( 'REQUEST_METHOD' => 'GET' );
		$this->assertTrue( $this->resource->is_request_allowed( $headers, $server ) );
	}

	/**
	 * Tests disallowed request headers against the Default Resource Isolation Policy.
	 *
	 * @since 0.0.1
	 */
	public function testResourceDisallowed() {
		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NO_CORS,
			Isolation_Policy::DEST => Isolation_Policy::DEST_SCRIPT,
		);
		$server  = array( 'REQUEST_METHOD' => 'GET' );
		$this->assertFalse( $this->resource->is_request_allowed( $headers, $server ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_DOCUMENT,
		);
		$server  = array( 'REQUEST_METHOD' => 'POST' );
		$this->assertFalse( $this->resource->is_request_allowed( $headers, $server ) );

		$headers = array(
			Isolation_Policy::SITE => Isolation_Policy::SITE_CROSS_SITE,
			Isolation_Policy::MODE => Isolation_Policy::MODE_NAVIGATE,
			Isolation_Policy::DEST => Isolation_Policy::DEST_OBJECT,
		);
		$server  = array( 'REQUEST_METHOD' => 'GET' );
		$this->assertFalse( $this->resource->is_request_allowed( $headers, $server ) );
	}
}
