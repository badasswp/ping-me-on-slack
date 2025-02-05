<?php
/**
 * Theme Class.
 *
 * This class is responsible for pinging theme events
 * to the Slack workspace.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Services;

use PingMeOnSlack\Abstracts\Service;
use PingMeOnSlack\Interfaces\Kernel;

class Theme extends Service implements Kernel {
	/**
	 * WP Theme.
	 *
	 * @since 1.1.3
	 *
	 * @var \WP_Theme
	 */
	public \WP_Theme $theme;

	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'switch_theme', [ $this, 'ping_on_theme_change' ], 10, 3 );
	}

	/**
	 * Ping on Theme change.
	 *
	 * Send notification to Slack channel when a
	 * Theme changes.
	 *
	 * @since 1.0.0
	 *
	 * @param string    $new_name  Name of the new theme.
	 * @param \WP_Theme $new_theme WP_Theme instance of the new theme.
	 * @param \WP_Theme $old_theme WP_Theme instance of the old theme.
	 */
	public function ping_on_theme_change( $new_name, $new_theme, $old_theme ) {
		// Get Theme.
		$this->theme = $new_theme;

		// Bail out, if not changed.
		if ( $old_theme === $new_theme ) {
			return;
		}

		$message = $this->get_message(
			esc_html__( 'A Theme was just switched!', 'ping-me-on-slack' )
		);

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
		$client = apply_filters( 'ping_me_on_slack_theme_client', $client = $this->get_client() );

		$client->ping( $message );
	}

	/**
	 * Get Message.
	 *
	 * This method returns the translated version
	 * of the Slack message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Slack Message.
	 * @return string
	 */
	public function get_message( $message ): string {
		$message = sprintf(
			"%s: %s \n%s: %s \n%s: %s \n%s: %s \n%s: %s",
			esc_html__( 'Ping', 'ping-me-on-slack' ),
			esc_html( $message ),
			esc_html__( 'ID', 'ping-me-on-slack' ),
			esc_html( $this->theme->ID ),
			esc_html__( 'Title', 'ping-me-on-slack' ),
			esc_html( $this->theme->title ),
			esc_html__( 'User', 'ping-me-on-slack' ),
			esc_html( wp_get_current_user()->user_login ),
			esc_html__( 'Date', 'ping-me-on-slack' ),
			esc_html( $this->get_date() )
		);

		/**
		 * Filter Ping Message.
		 *
		 * Set custom Slack message to be sent when the
		 * user switches a Theme.
		 *
		 * @since 1.0.0
		 *
		 * @param string    $message Slack Message.
		 * @param \WP_Theme $theme   WP Theme.
		 *
		 * @return string
		 */
		return (string) apply_filters( 'ping_me_on_slack_theme_message', $message, $this->theme );
	}
}
