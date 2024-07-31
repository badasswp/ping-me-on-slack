<?php

namespace PingMySlack\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMySlack\Core\Client;
use PingMySlack\Services\Post;

/**
 * @covers \PingMySlack\Services\Post::__construct
 * @covers \PingMySlack\Services\Post::register
 * @covers \PingMySlack\Services\Post::ping_on_post_status_change
 * @covers \PingMySlack\Services\Post::get_message
 */
class PostTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->client = Mockery::mock( Client::class )->makePartial();
		$this->client->shouldAllowMockingProtectedMethods();

		$this->post = Mockery::mock( Post::class )->makePartial();
		$this->post->shouldAllowMockingProtectedMethods();
		$this->post->client = $this->client;
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'transition_post_status', [ $this->post, 'ping_on_post_status_change' ], 10, 3 );

		$this->post->register();

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_bails_if_status_is_unchanged() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		$this->post->ping_on_post_status_change( 'draft', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_bails_if_new_status_is_auto_draft() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		$this->post->ping_on_post_status_change( 'auto-draft', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		$this->post->shouldReceive( 'get_message' )
			->once()
			->with( 'A Post was just published!' )
			->andReturn( 'A Post was just published!' );

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'A Post was just published!' );

		\WP_Mock::expectFilter( 'ping_my_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'publish', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_get_message() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		$user             = Mockery::mock( \WP_User::class )->makePartial();
		$user->user_login = 'john@doe.com';

		$post->ID          = 1;
		$post->post_author = 1;
		$post->post_title  = 'Hello World!';
		$post->post_type   = 'post';

		$this->post->event = 'publish';
		$this->post->post  = $post;

		\WP_Mock::userFunction( 'get_user_by' )
			->once()
			->with( 'id', 1 )
			->andReturn( $user );

		$this->post->shouldReceive( 'get_date' )
			->once()
			->with()
			->andReturn( '08:57:13, 01-07-2024' );

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 6,
				'return' => function ( $text, $domain = 'ping-my-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_html',
			[
				'times'  => 4,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$message = "Ping: A Post was just published! \nID: 1 \nTitle: Hello World! \nUser: john@doe.com \nDate: 08:57:13, 01-07-2024";

		\WP_Mock::expectFilter(
			'ping_my_slack_post_message',
			$message,
			$post,
			'publish'
		);

		$expected = $this->post->get_message( 'A Post was just published!' );

		$this->assertSame( $expected, $message );
		$this->assertConditionsMet();
	}
}
