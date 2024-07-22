<?php
/**
 * Comment Class.
 *
 * This class is responsible for pinging comment events
 * to the Slack workspace.
 *
 * @package PingMySlack
 */

namespace PingMySlack\Services;

use PingMySlack\Core\Client;
use PingMySlack\Abstracts\Service;
use PingMySlack\Interfaces\Kernel;

class Comment extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'transition_comment_status', [ $this, 'ping_on_comment_status_change' ], 10, 3 );
	}

	/**
	 * Ping on Comment Status change.
	 *
	 * Send notification to Slack channel when a
	 * Comment status changes.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $new_status New Status.
	 * @param string      $old_status Old Status.
	 * @param \WP_Comment $comment    WP Comment.
	 *
	 * @return void
	 */
	public function ping_on_comment_status_change( $new_status, $old_status, $comment ): void {
		// Get Comment.
		$this->comment = $comment;

		// Bail out, if not changed.
		if ( $old_status === $new_status ) {
			return;
		}

		// Get Event Type.
		$this->event = $new_status;

		switch ( $new_status ) {
			case 'approved':
				$message = $this->get_message( 'A Comment was just published!' );
				break;

			case 'trash':
				$message = $this->get_message( 'A Comment was just trashed!' );
				break;
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
		$this->client = apply_filters( 'ping_my_slack_comment_client', $client = $this->client );

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
			esc_html__( 'Comment', 'ping-my-slack' ),
			esc_html( $this->comment->comment_content ),
			esc_html__( 'User', 'ping-my-slack' ),
			esc_html( $this->comment->comment_author_email ),
			esc_html__( 'Post', 'ping-my-slack' ),
			esc_html( get_the_title( $this->comment->comment_post_ID ) ),
			esc_html__( 'Date', 'ping-my-slack' ),
			esc_html( $this->get_date() )
		);

		/**
		 * Filter Ping Message.
		 *
		 * Set custom Slack message to be sent when the
		 * user hits the publish button.
		 *
		 * @since 1.0.0
		 *
		 * @param string      $message Slack Message.
		 * @param \WP_Comment $comment WP Comment.
		 * @param string      $event   Event Type.
		 *
		 * @return string
		 */
		return (string) apply_filters( 'ping_my_slack_comment_message', $message, $this->comment, $this->event );
	}
}
