<?php
/**
 * Comment Class.
 *
 * This class is responsible for pinging comment events
 * to the Slack workspace.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Services;

use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Abstracts\Service;
use PingMeOnSlack\Interfaces\Kernel;

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
	 * @since 1.1.0 Implement custom message feature via plugin options.
	 *
	 * @param string      $new_status New Status.
	 * @param string      $old_status Old Status.
	 * @param \WP_Comment $comment    WP Comment.
	 *
	 * @return void
	 */
	public function ping_on_comment_status_change( $new_status, $old_status, $comment ): void {
		// Bail out early, if not enabled.
		if ( ! pmos_get_settings( 'enable_comment' ) ) {
			return;
		}

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
				$comment_approve = pmos_get_settings( 'comment_approve' );

				$message = esc_html__( 'A Comment was just approved!', 'ping-me-on-slack' );
				$message = empty( $comment_approve ) ? $message : $comment_approve;
				$message = $this->get_message( $message );
				break;

			case 'trash':
				$comment_trash = pmos_get_settings( 'comment_trash' );

				$message = esc_html__( 'A Comment was just trashed!', 'ping-me-on-slack' );
				$message = empty( $comment_trash ) ? $message : $comment_trash;
				$message = $this->get_message( $message );
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
		$this->client = apply_filters( 'ping_me_on_slack_comment_client', $client = $this->client );

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
			"%s: %s \n%s: %s \n%s: %s \n%s: %s \n%s: %s",
			esc_html__( 'Ping', 'ping-me-on-slack' ),
			esc_html( $message ),
			esc_html__( 'Comment', 'ping-me-on-slack' ),
			esc_html( $this->comment->comment_content ),
			esc_html__( 'User', 'ping-me-on-slack' ),
			esc_html( $this->comment->comment_author_email ),
			esc_html__( 'Post', 'ping-me-on-slack' ),
			esc_html( get_the_title( $this->comment->comment_post_ID ) ),
			esc_html__( 'Date', 'ping-me-on-slack' ),
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
		return (string) apply_filters( 'ping_me_on_slack_comment_message', $message, $this->comment, $this->event );
	}
}
