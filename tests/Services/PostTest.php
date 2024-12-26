<?php

namespace PingMeOnSlack\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Services\Post;

/**
 * @covers \PingMeOnSlack\Services\Post::__construct
 * @covers \PingMeOnSlack\Services\Post::register
 * @covers \PingMeOnSlack\Services\Post::ping_on_post_status_change
 * @covers \PingMeOnSlack\Services\Post::get_message
 * @covers pmos_get_settings
 */
class PostTest extends TestCase {
	public Client $client;
	public Post $post;

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

	public function test_ping_on_post_status_change_fails() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_user' => false,
				]
			);

		$this->post->ping_on_post_status_change( 'draft', 'publish', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_bails_if_status_is_unchanged() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_user' => true,
				]
			);

		$this->post->ping_on_post_status_change( 'draft', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_bails_if_new_status_is_auto_draft() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post' => true,
				]
			);

		$this->post->ping_on_post_status_change( 'auto-draft', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes_on_publish() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->post_type = 'post';

		$this->post->post = $post;

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post'  => true,
					'post_publish' => '',
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

		$this->post->shouldReceive( 'get_message' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'A Post was just published!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'publish', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes_on_publish_with_custom_post_option() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->post_type = 'post';

		$this->post->post = $post;

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post'  => true,
					'post_publish' => 'Custom Message: Your post is now published!',
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

		$this->post->shouldReceive( 'get_message' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'Custom Message: Your post is now published!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'publish', 'draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes_on_draft() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->post_type = 'post';

		$this->post->post = $post;

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post' => true,
					'post_draft'  => '',
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

		$this->post->shouldReceive( 'get_message' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'A Post draft was just created!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'draft', 'auto-draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes_on_draft_with_custom_post_option() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->post_type = 'post';

		$this->post->post = $post;

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post' => true,
					'post_draft'  => 'Custom Message: Your post is now drafted!',
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

		$this->post->shouldReceive( 'get_message' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'Custom Message: Your post is now drafted!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'draft', 'auto-draft', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes_on_trash() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->post_type = 'post';

		$this->post->post = $post;

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post' => true,
					'post_trash'  => '',
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

		$this->post->shouldReceive( 'get_message' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'A Post was just trashed!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'trash', 'publish', $post );

		$this->assertConditionsMet();
	}

	public function test_ping_on_post_status_change_passes_on_trash_with_custom_post_option() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->post_type = 'post';

		$this->post->post = $post;

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post' => true,
					'post_trash'  => 'Custom Message: Your post is now trashed!',
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

		$this->post->shouldReceive( 'get_message' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'Custom Message: Your post is now trashed!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_post_client', $this->client );

		$this->post->ping_on_post_status_change( 'trash', 'publish', $post );

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

		$message = "Ping: A Post was just published! \nID: 1 \nTitle: Hello World! \nUser: john@doe.com \nDate: 08:57:13, 01-07-2024";

		\WP_Mock::expectFilter(
			'ping_me_on_slack_post_message',
			$message,
			$post,
			'publish'
		);

		$expected = $this->post->get_message( 'A Post was just published!' );

		$this->assertSame( $expected, $message );
		$this->assertConditionsMet();
	}
}
