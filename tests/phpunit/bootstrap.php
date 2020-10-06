<?php
/**
 * Unit tests bootstrap script.
 *
 * @package   Google\WP_Fetch_Metadata
 * @copyright 2020 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/fetch-metadata
 */

// Detect project directory.
define( 'TESTS_PLUGIN_DIR', dirname( dirname( __DIR__ ) ) );

// Disable xdebug backtrace.
if ( function_exists( 'xdebug_disable' ) ) {
	xdebug_disable();
}

require_once TESTS_PLUGIN_DIR . '/vendor/autoload.php';

// PHPUnit < 6.0 compatibility shim.
require_once __DIR__ . '/phpunit-compat.php';
