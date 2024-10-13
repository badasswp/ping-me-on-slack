<?php
/**
 * Access Class.
 *
 * This class handles the pinging of user login/logout
 * events to the Slack workspace.
 *
 * @package PingMeOnSlack;
 */

namespace PingMeOnSlack\Services;

use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Abstracts\Service;
use PingMeOnSlack\Interfaces\Kernel;

class Access extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_login', [ $this, 'ping_on_user_login' ], 10, 2 );
		add_action( 'wp_logout', [ $this, 'ping_on_user_logout' ] );
	}

	/**
	 * Ping on User login.
	 *
	 * This method sends event logging to the Slack Workspace
	 * on user login.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Implement custom message feature via plugin options.
	 *
	 * @param string   $user_login User Login.
	 * @param \WP_User $user       WP User.
	 *
	 * @return void
	 */
	public function ping_on_user_login( $user_login, $user ): void {
		// Bail out early, if not enabled.
		if ( ! pmos_get_settings( 'enable_access' ) ) {
			return;
		}

		/**
		 * Filter Slack Client.
		 *
		 * Customise the Client instance here, you can
		 * make this extensible.
		 *
		 * @since 1.0.0
		 *
		 * @param Client $client Client Instance.
		 * @return Client
		 */
		$this->client = apply_filters( 'ping_me_on_slack_login_client', $client = $this->client );

		$access_login = pmos_get_settings( 'access_login' );

		$message = esc_html__( 'A User just logged in!', 'ping-me-on-slack' );
		$message = empty( $access_login ) ? $message : $access_login;
		$message = sprintf(
			"%s: %s \n%s: %s \n%s: %s \n%s: %s",
			esc_html__( 'Ping', 'ping-me-on-slack' ),
			esc_html( $message ),
			esc_html__( 'ID', 'ping-me-on-slack' ),
			esc_html( $user->ID ),
			esc_html__( 'User', 'ping-me-on-slack' ),
			esc_html( $user_login ),
			esc_html__( 'Date', 'ping-me-on-slack' ),
			esc_html( $this->get_date() )
		);

		/**
		 * Filter Ping Message.
		 *
		 * Set custom Slack message to be sent when the
		 * user logs in.
		 *
		 * @since 1.0.0
		 *
		 * @param string   $message Slack Message.
		 * @param \WP_User $user    WP User.
		 *
		 * @return string
		 */
		$message = (string) apply_filters( 'ping_me_on_slack_login_message', $message, $user );

		$this->client->ping( $message );
	}

	/**
	 * Ping on User logout.
	 *
	 * This method sends event logging to the Slack Workspace
	 * on user logout.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Implement custom message feature via plugin options.
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function ping_on_user_logout( $user_id ): void {
		// Bail out early, if not enabled.
		if ( ! pmos_get_settings( 'enable_access' ) ) {
			return;
		}

		/**
		 * Filter Slack Client.
		 *
		 * Customise the Client instance here, you can
		 * make this extensible.
		 *
		 * @since 1.0.0
		 *
		 * @param Client $client Client Instance.
		 * @return Client
		 */
		$this->client = apply_filters( 'ping_me_on_slack_logout_client', $client = $this->client );

		$user = get_user_by( 'id', $user_id );

		$access_logout = pmos_get_settings( 'access_logout' );

		$message = esc_html__( 'A User just logged out!', 'ping-me-on-slack' );
		$message = empty( $access_logout ) ? $message : $access_logout;
		$message = sprintf(
			"%s: %s \n%s: %s \n%s: %s \n%s: %s",
			esc_html__( 'Ping', 'ping-me-on-slack' ),
			esc_html( $message ),
			esc_html__( 'ID', 'ping-me-on-slack' ),
			esc_html( $user_id ),
			esc_html__( 'User', 'ping-me-on-slack' ),
			esc_html( $user->user_login ),
			esc_html__( 'Date', 'ping-me-on-slack' ),
			esc_html( $this->get_date() )
		);

		/**
		 * Filter Ping Message.
		 *
		 * Set custom Slack message to be sent when the
		 * user logs out.
		 *
		 * @since 1.0.0
		 *
		 * @param string   $message Slack Message.
		 * @param \WP_User $user    WP User.
		 *
		 * @return string
		 */
		$message = (string) apply_filters( 'ping_me_on_slack_logout_message', $message, $user );

		$this->client->ping( $message );
	}
}
