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
	 * Get Slack Client.
	 *
	 * @since 1.1.3
	 *
	 * @return SlackClient
	 */
	protected function get_slack_client(): SlackClient {
		return new SlackClient(
			pmos_get_settings( 'webhook' ),
			[
				'channel'  => pmos_get_settings( 'channel' ),
				'username' => pmos_get_settings( 'username' ),
			]
		);
	}

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
		try {
			$this->get_slack_client()->send( $message );
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
			 * @param string $e Exception error message.
			 * @return void
			 */
			do_action( 'ping_me_on_slack_on_ping_error', $e->getMessage() );
		}
	}
}
