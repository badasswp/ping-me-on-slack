<?php
/**
 * Plugin Name: Ping Me On Slack
 * Plugin URI:  https://github.com/badasswp/ping-me-on-slack
 * Description: Get notifications on Slack when changes are made on your WP website.
 * Version:     1.1.1
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ping-me-on-slack
 * Domain Path: /languages
 *
 * @package PingMeOnSlack
 */

namespace badasswp\PingMeOnSlack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PING_ME_ON_SLACK', __DIR__ . '/vendor/autoload.php' );

/**
 * Run Notice, if Composer is not installed.
 *
 * @since 1.0.0
 * @return void
 */
function ping_me_on_slack_notice(): void {
	echo esc_html__( 'Error: Composer is not installed!', 'ping-me-on-slack' );
}

/**
 * Run Plugin.
 *
 * @since 1.0.0
 *
 * @param string $autoload Composer Autoload file.
 * @return void
 */
function ping_me_on_slack_run( $autoload ): void {
	require_once $autoload;
	require_once __DIR__ . '/inc/Helpers/functions.php';
	( \PingMeOnSlack\Plugin::get_instance() )->run();
}

// Bail out, if Composer is NOT installed.
if ( ! file_exists( PING_ME_ON_SLACK ) ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\ping_me_on_slack_notice' );
	return;
}

// Run Plugin.
ping_me_on_slack_run( PING_ME_ON_SLACK );
