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
	 * Slack Client.
	 *
	 * @var SlackClient
	 */
	protected SlackClient $client;

	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		$this->client = $this->get_client();
	}

	/**
	 * Setter function.
	 *
	 * @since 1.2.0
	 *
	 * @param string $name   Class prop.
	 * @param mixed  $params Slack params.
	 */
	public function __set( $name, $params ) {
		if ( 'args' === $name ) {
			$this->client = $this->get_client( $params );
		}
	}

	/**
	 * Get Slack Client.
	 *
	 * @since 1.1.3
	 * @since 1.2.0 Introduce Slack Params.
	 *
	 * @params mixed[] $params Slack Params.
	 * @return SlackClient
	 */
	protected function get_client( $params = [] ): SlackClient {
		$args = wp_parse_args(
			$params,
			[
				'channel'  => pmos_get_settings( 'channel' ),
				'username' => pmos_get_settings( 'username' ),
			]
		);

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
			$this->client->send( $message );
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
