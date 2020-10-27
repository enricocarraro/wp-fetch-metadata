<?php
/**
 * Class Google\WP_Fetch_Metadata\Tests\PHPUnit\Unit\Policies_Tests
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Tests\PHPUnit\Integration;

use Google\WP_Fetch_Metadata\Tests\PHPUnit\Framework\Unit_Test_Case;

use Google\WP_Fetch_Metadata\Policy\Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Navigation_Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy\Default_Resource_Isolation_Policy;
use Google\WP_Fetch_Metadata\Policy_Registry;
use Google\WP_Fetch_Metadata\Policies;


/**
 * Class that tests the Policies class.
 */
class Policies_Tests extends Unit_Test_Case {

	/**
	 * Tests get_all() when the policy registry is empty.
	 *
	 * @since 0.0.1
	 */
	public function testGetAllEmptyRegistry() {
		$policies = new Policies( new Policy_Registry() );

		$this->assertEquals( array(), $policies->get_all() );
	}

	/**
	 * Tests get_all() when the policy registry contains the default policies.
	 *
	 * @since 0.0.1
	 */
	public function testGetAllDefaultPolicies() {
		$navigation = new Default_Navigation_Isolation_Policy( 'default-navigation', 'Default Navigation Isolation Policy' );
		$resource   = new Default_Resource_Isolation_Policy( 'default-resource', 'Default Resource Isolation Policy' );

		$policy_registry = new Policy_Registry();
		$policy_registry->register( $navigation );
		$policy_registry->register( $resource );

		$policies = new Policies( $policy_registry );

		$this->assertEquals(
			array(
				$navigation->slug => $navigation,
				$resource->slug   => $resource,
			),
			$policies->get_all()
		);
	}
}
