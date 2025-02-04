<?php
/**
 * Client Class.
 *
 * This class handles sending Slack notifications
 * via API calls.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Core;

use Maknz\Slack\Client as SlackClient;
use PingMeOnSlack\Interfaces\Dispatcher;

class Client implements Dispatcher {
	/**
	 * Ping Slack.
	 *
	 * This method handles the Remote POST calls
	 * to Slack API endpoints.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Slack Message.
	 * @return void
	 */
	public function ping( $message ): void {
		$settings = get_option( 'ping_me_on_slack', [] );

		$slack = new SlackClient(
			$settings['webhook'] ?? '',
			[
				'channel'  => $settings['channel'] ?? '',
				'username' => $settings['username'] ?? '',
			]
		);

		try {
			$slack->send( $message );
		} catch ( \Exception $e ) {
			error_log(
				sprintf(
					'Fatal Error: Something went wrong... %s',
					$e->getMessage()
				)
			);

			/**
			 * Fire after Exception is caught.
			 *
			 * This action provides a way to use the caught
			 * exception for logging purposes.
			 *
			 * @since 1.0.0
			 *
			 * @param \RuntimeException $e Exception object.
			 * @return void
			 */
			do_action( 'ping_me_on_slack_on_ping_error', $e );
		}
	}
}
