<?php
/**
 * Boot Class.
 *
 * Define registration hooks, translations and
 * post meta definitions here.
 *
 * @package PingMeOnSlack
 */

namespace PingMySlack\Services;

use PingMySlack\Abstracts\Service;
use PingMySlack\Interfaces\Kernel;

class Boot extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'ping_me_on_slack_translation' ] );
	}

	/**
	 * Register Text Domain.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ping_me_on_slack_translation(): void {
		load_plugin_textdomain(
			'ping-my-slack',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/../../languages'
		);
	}
}
