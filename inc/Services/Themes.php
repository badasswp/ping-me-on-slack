<?php
/**
 * Themes Class.
 *
 * This class is responsible for pinging theme events
 * to the Slack workspace.
 *
 * @package PingMySlack
 */

namespace PingMySlack\Services;

use PingMySlack\Abstracts\Service;
use PingMySlack\Interfaces\Kernel;

class Themes extends Service implements Kernel {
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

		$message = $this->get_message( 'A Theme was just switched!' );

		$this->client->ping( $message );
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
			"Ping: %s \n%s: %s \n%s: %s \n%s: %s \n%s: %s",
			esc_html__( $message, 'ping-my-slack' ),
			esc_html__( 'ID', 'ping-my-slack' ),
			esc_html( $this->theme->ID ),
			esc_html__( 'Title', 'ping-my-slack' ),
			esc_html( $this->theme->title ),
			esc_html__( 'User', 'ping-my-slack' ),
			esc_html( get_user_by( 'id', $this->post->post_author )->user_login ),
			esc_html__( 'Date', 'ping-my-slack' ),
			esc_html( gmdate( 'H:i:s, d-m-Y' ) )
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
		return apply_filters( 'ping_my_slack_theme_message', $message, $this->theme );
	}
}
