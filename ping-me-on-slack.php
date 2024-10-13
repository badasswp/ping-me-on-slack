<?php
/**
 * Plugin Name: Ping Me On Slack
 * Plugin URI:  https://github.com/badasswp/ping-me-on-slack
 * Description: Get notifications on Slack when changes are made on your WP website.
 * Version:     1.0.1
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

require_once __DIR__ . '/init.php';

// Bail out, if Composer is NOT installed.
if ( ! file_exists( PING_ME_ON_SLACK ) ) {
	add_action( 'admin_notices', 'ping_me_on_slack_notice' );
	return;
}

// Run Plugin.
ping_me_on_slack_run( PING_ME_ON_SLACK );
