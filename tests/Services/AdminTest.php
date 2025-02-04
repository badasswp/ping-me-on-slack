<?php

namespace PingMeOnSlack\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Services\Admin;

/**
 * @covers \PingMeOnSlack\Services\Admin::register
 * @covers \PingMeOnSlack\Services\Admin::register_options_menu
 * @covers \PingMeOnSlack\Services\Admin::register_options_init
 * @covers \PingMeOnSlack\Services\Admin::register_options_styles
 * @covers \PingMeOnSlack\Admin\Options::__callStatic
 * @covers \PingMeOnSlack\Admin\Options::get_form_fields
 * @covers \PingMeOnSlack\Admin\Options::get_form_notice
 * @covers \PingMeOnSlack\Admin\Options::get_form_page
 * @covers \PingMeOnSlack\Admin\Options::get_form_submit
 * @covers \PingMeOnSlack\Admin\Options::init
 */
class AdminTest extends TestCase {
	public Admin $admin;

	public function setUp(): void {
		\WP_Mock::setUp();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn( [] );

		$this->admin = new Admin();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'admin_init', [ $this->admin, 'register_options_init' ] );
		\WP_Mock::expectActionAdded( 'admin_menu', [ $this->admin, 'register_options_menu' ] );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $this->admin, 'register_options_styles' ] );

		$this->admin->register();

		$this->assertConditionsMet();
	}

	public function test_register_options_menu() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'add_menu_page' )
			->once()
			->with(
				'Ping Me On Slack',
				'Ping Me On Slack',
				'manage_options',
				'ping-me-on-slack',
				[ $this->admin, 'register_options_page' ],
				'dashicons-format-chat',
				100
			)
			->andReturn( null );

		$menu = $this->admin->register_options_menu();

		$this->assertNull( $menu );
		$this->assertConditionsMet();
	}

	public function test_register_options_init_bails_out_if_any_nonce_settings_is_missing() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$_POST = [
			'ping_me_on_slack_save_settings' => true,
		];

		$settings = $this->admin->register_options_init();

		$this->assertNull( $settings );
		$this->assertConditionsMet();
	}

	public function test_register_options_init_bails_out_if_nonce_verification_fails() {
		$_POST = [
			'ping_me_on_slack_save_settings'  => true,
			'ping_me_on_slack_settings_nonce' => 'a8vbq3cg3sa',
		];

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'wp_unslash' )
			->times( 1 )
			->with( 'a8vbq3cg3sa' )
			->andReturn( 'a8vbq3cg3sa' );

		\WP_Mock::userFunction( 'sanitize_text_field' )
			->times( 1 )
			->with( 'a8vbq3cg3sa' )
			->andReturn( 'a8vbq3cg3sa' );

		\WP_Mock::userFunction( 'wp_verify_nonce' )
			->once()
			->with( 'a8vbq3cg3sa', 'ping_me_on_slack_settings_action' )
			->andReturn( false );

		$settings = $this->admin->register_options_init();

		$this->assertNull( $settings );
		$this->assertConditionsMet();
	}

	public function test_register_options_init_passes() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$_POST = [
			'ping_me_on_slack_save_settings'  => true,
			'ping_me_on_slack_settings_nonce' => 'a8vbq3cg3sa',
		];

		\WP_Mock::userFunction(
			'wp_unslash',
			[
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'sanitize_text_field' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'wp_verify_nonce' )
			->times( 1 )
			->with( 'a8vbq3cg3sa', 'ping_me_on_slack_settings_action' )
			->andReturn( true );

		\WP_Mock::userFunction( 'update_option' )
			->once()
			->with(
				'ping_me_on_slack',
				[
					'enable_user'     => '',
					'user_create'     => '',
					'user_modify'     => '',
					'user_delete'     => '',
					'enable_access'   => '',
					'access_login'    => '',
					'access_logout'   => '',
					'enable_comment'  => '',
					'comment_approve' => '',
					'comment_trash'   => '',
					'enable_post'     => '',
					'post_draft'      => '',
					'post_publish'    => '',
					'post_trash'      => '',
					'username'        => '',
					'channel'         => '',
					'webhook'         => '',
				]
			)
			->andReturn( null );

		$settings = $this->admin->register_options_init();

		$this->assertNull( $settings );
		$this->assertConditionsMet();
	}

	public function test_register_options_styles_passes() {
		$screen = Mockery::mock( \WP_Screen::class )->makePartial();
		$screen->shouldAllowMockingProtectedMethods();
		$screen->id = 'toplevel_page_ping-me-on-slack';

		\WP_Mock::userFunction( 'get_current_screen' )
			->andReturn( $screen );

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'plugins_url' )
			->with( 'ping-me-on-slack/styles.css' )
			->andReturn( 'https://example.com/wp-content/plugins/ping-me-on-slack/styles.css' );

		\WP_Mock::userFunction( 'wp_enqueue_style' )
			->with(
				'ping-me-on-slack',
				'https://example.com/wp-content/plugins/ping-me-on-slack/styles.css',
				[],
				true,
				'all'
			)
			->andReturn( null );

		$this->admin->register_options_styles();

		$this->assertConditionsMet();
	}

	public function test_register_options_styles_bails() {
		\WP_Mock::userFunction( 'get_current_screen' )
			->andReturn( '' );

		$this->admin->register_options_styles();

		$this->assertConditionsMet();
	}
}
