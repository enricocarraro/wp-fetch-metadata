<?php
/**
 * Class Google\WP_Fetch_Metadata\Tests\PHPUnit\Integration\Sample_Tests
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

namespace Google\WP_Fetch_Metadata\Tests\PHPUnit\Integration;

use Google\WP_Fetch_Metadata\Tests\PHPUnit\Framework\Integration_Test_Case;

/**
 * Class containing a sample test.
 */
class Sample_Tests extends Integration_Test_Case {

	/**
	 * Performs a sample test.
	 */
	public function testNothingUseful() {
		$this->assertTrue( defined( 'ABSPATH' ) );
	}
}
