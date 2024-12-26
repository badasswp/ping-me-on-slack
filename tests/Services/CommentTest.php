<?php

namespace PingMeOnSlack\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Services\Comment;

/**
 * @covers \PingMeOnSlack\Services\Comment::__construct
 * @covers \PingMeOnSlack\Services\Comment::register
 * @covers \PingMeOnSlack\Services\Comment::ping_on_comment_status_change
 * @covers \PingMeOnSlack\Services\Comment::get_message
 * @covers pmos_get_settings
 */
class CommentTest extends TestCase {
	public Client $client;
	public Comment $comment;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->client = Mockery::mock( Client::class )->makePartial();
		$this->client->shouldAllowMockingProtectedMethods();

		$this->comment = Mockery::mock( Comment::class )->makePartial();
		$this->comment->shouldAllowMockingProtectedMethods();
		$this->comment->client = $this->client;
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'transition_comment_status', [ $this->comment, 'ping_on_comment_status_change' ], 10, 3 );

		$this->comment->register();

		$this->assertConditionsMet();
	}

	public function test_ping_on_comment_status_change_bails() {
		$comment = Mockery::mock( \WP_Comment::class )->makePartial();
		$comment->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_comment' => true,
				]
			);

		$this->comment->ping_on_comment_status_change( 'approved', 'approved', $comment );

		$this->assertConditionsMet();
	}

	public function test_ping_on_comment_status_change_fails_on_custom_option_false() {
		$comment = Mockery::mock( \WP_Comment::class )->makePartial();
		$comment->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_comment' => false,
				]
			);

		$this->comment->ping_on_comment_status_change( 'trash', 'approved', $comment );

		$this->assertConditionsMet();
	}

	public function test_ping_on_comment_status_change_passes_on_trash() {
		$comment = Mockery::mock( \WP_Comment::class )->makePartial();
		$comment->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_comment' => true,
				]
			);

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'comment_trash' => '',
				]
			);

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 1,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$this->comment->shouldReceive( 'get_message' )
			->once()
			->with( 'A Comment was just trashed!' )
			->andReturn( 'A Comment was just trashed!' );

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'A Comment was just trashed!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_comment_client', $this->client );

		$this->comment->ping_on_comment_status_change( 'trash', 'approved', $comment );

		$this->assertConditionsMet();
	}

	public function test_get_message() {
		$comment = Mockery::mock( \WP_Comment::class )->makePartial();
		$comment->shouldAllowMockingProtectedMethods();

		$comment->comment_content      = 'What a wonderful world!';
		$comment->comment_author_email = 'john@doe.com';
		$comment->comment_post_ID      = 1;

		$this->comment->event   = 'trash';
		$this->comment->comment = $comment;

		$this->comment->shouldReceive( 'get_date' )
			->once()
			->with()
			->andReturn( '08:57:13, 01-07-2024' );

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 5,
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_html',
			[
				'times'  => 5,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'get_the_title' )
			->once()
			->with( 1 )
			->andReturn( 'Hello World!' );

		$message = "Ping: A Comment was just trashed! \nComment: What a wonderful world! \nUser: john@doe.com \nPost: Hello World! \nDate: 08:57:13, 01-07-2024";

		\WP_Mock::expectFilter(
			'ping_me_on_slack_comment_message',
			$message,
			$comment,
			'trash'
		);

		$expected = $this->comment->get_message( 'A Comment was just trashed!' );

		$this->assertSame( $expected, $message );
		$this->assertConditionsMet();
	}
}
