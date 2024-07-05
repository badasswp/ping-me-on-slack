<?php
/**
 * Post Class.
 *
 * This class binds all Post, Page, CPT logic
 * to the WP API.
 *
 * @package PingMySlack
 */

namespace PingMySlack;

class Post extends Service {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'transition_post_status', [ $this, 'ping_on_post_creation' ], 10, 3 );
	}

	/**
	 * Ping on Post Creation.
	 *
	 * Send notification to Slack channel when a
	 * Post is created.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    WP Post.
	 *
	 * @return void
	 */
	public function ping_on_post_creation( $post_id, $post, $is_update ): void {
		if ( wp_is_post_revision( $post_id ) || 'post' !== $post->post_type || $is_update ) {
			return;
		}

		$message = sprintf(
			'A Post was just created! ID: %s, Post Title: %s',
			esc_html( $post_id ),
			esc_html( get_post_field( 'post_title', $post_id ) )
		);

		/**
		 * Filter Ping Message.
		 *
		 * Set custom Slack message to be sent when the
		 * user hits the publish button.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		$message = apply_filters( 'ping_my_slack_message', $message );

		$this->client->ping( $message );
	}
}
