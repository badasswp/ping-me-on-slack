<?php
/**
 * Post Class.
 *
 * This class binds all Post, Page, CPT logic
 * to the WP API.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Services;

use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Abstracts\Service;
use PingMeOnSlack\Interfaces\Kernel;

class Post extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Add stop-gap via `enable_post` plugin option.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( pmos_get_settings( 'enable_post' ) ) {
			add_action( 'transition_post_status', [ $this, 'ping_on_post_status_change' ], 10, 3 );
		}
	}

	/**
	 * Ping on Post Status change.
	 *
	 * Send notification to Slack channel when a
	 * Post status changes.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $new_status New Status.
	 * @param string   $old_status Old Status.
	 * @param \WP_Post $post       WP Post.
	 *
	 * @return void
	 */
	public function ping_on_post_status_change( $new_status, $old_status, $post ): void {
		// Get Post.
		$this->post = $post;

		// Bail out, if not changed.
		if ( $old_status === $new_status || 'auto-draft' === $new_status ) {
			return;
		}

		// Get Event Type.
		$this->event = $new_status;

		switch ( $new_status ) {
			case 'draft':
				$message = $this->get_message(
					esc_html__( 'A Post draft was just created!', 'ping-me-on-slack' )
				);
				break;

			case 'publish':
				$message = $this->get_message(
					esc_html__( 'A Post was just published!', 'ping-me-on-slack' )
				);
				break;

			case 'trash':
				$message = $this->get_message(
					esc_html__( 'A Post was just trashed!', 'ping-me-on-slack' )
				);
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
		$this->client = apply_filters( "ping_me_on_slack_{$this->post->post_type}_client", $client = $this->client );

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
			esc_html__( 'ID', 'ping-me-on-slack' ),
			esc_html( $this->post->ID ),
			esc_html__( 'Title', 'ping-me-on-slack' ),
			esc_html( $this->post->post_title ),
			esc_html__( 'User', 'ping-me-on-slack' ),
			esc_html( get_user_by( 'id', $this->post->post_author )->user_login ),
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
		 * @param string   $message Slack Message.
		 * @param \WP_Post $post    WP Post.
		 * @param string   $event   Event Type.
		 *
		 * @return string
		 */
		return (string) apply_filters( "ping_me_on_slack_{$this->post->post_type}_message", $message, $this->post, $this->event );
	}
}
