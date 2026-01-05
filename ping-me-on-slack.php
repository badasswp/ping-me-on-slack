<?php
/**
 * Plugin Name: Ping Me On Slack
 * Plugin URI:  https://github.com/badasswp/ping-me-on-slack
 * Description: Get notifications on Slack when changes are made on your WP website.
 * Version:     1.2.2
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

if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'PMOS_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( PMOS_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'ping-me-on-slack' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Run Plugin.
require_once PMOS_AUTOLOAD;
require_once __DIR__ . '/inc/Helpers/functions.php';
( \PingMeOnSlack\Plugin::get_instance() )->run();
