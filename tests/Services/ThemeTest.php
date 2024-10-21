<?php

namespace PingMeOnSlack\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Services\Theme;

/**
 * @covers \PingMeOnSlack\Services\Theme::__construct
 * @covers \PingMeOnSlack\Services\Theme::register
 * @covers \PingMeOnSlack\Services\Theme::ping_on_theme_change
 * @covers \PingMeOnSlack\Services\Theme::get_message
 */
class ThemeTest extends TestCase {
	public Client $client;
	public Theme $theme;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->client = Mockery::mock( Client::class )->makePartial();
		$this->client->shouldAllowMockingProtectedMethods();

		$this->theme = Mockery::mock( Theme::class )->makePartial();
		$this->theme->shouldAllowMockingProtectedMethods();
		$this->theme->client = $this->client;
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'switch_theme', [ $this->theme, 'ping_on_theme_change' ], 10, 3 );

		$this->theme->register();

		$this->assertConditionsMet();
	}

	public function test_ping_on_theme_change_bails_if_theme_is_unchanged() {
		$theme = Mockery::mock( \WP_Theme::class )->makePartial();
		$theme->shouldAllowMockingProtectedMethods();

		$this->theme->ping_on_theme_change( 'Elementor', $theme, $theme );

		$this->assertConditionsMet();
	}

	public function test_ping_on_theme_change_passes() {
		$theme1 = Mockery::mock( \WP_Theme::class )->makePartial();
		$theme1->shouldAllowMockingProtectedMethods();

		$theme2 = Mockery::mock( \WP_Theme::class )->makePartial();
		$theme2->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 1,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$this->theme->shouldReceive( 'get_message' )
			->once()
			->with( 'A Theme was just switched!' )
			->andReturn( 'A Theme was just switched!' );

		$this->client->shouldReceive( 'ping' )
			->once()
			->with( 'A Theme was just switched!' );

		\WP_Mock::expectFilter( 'ping_me_on_slack_theme_client', $this->client );

		$this->theme->ping_on_theme_change( 'Divi', $theme1, $theme2 );

		$this->assertConditionsMet();
	}

	public function test_get_message() {
		$theme = Mockery::mock( \WP_Theme::class )->makePartial();
		$theme->shouldAllowMockingProtectedMethods();

		$user             = Mockery::mock( \WP_User::class )->makePartial();
		$user->user_login = 'john@doe.com';

		$theme->ID    = 1;
		$theme->title = 'Diva';

		$this->theme->theme = $theme;

		\WP_Mock::userFunction( 'wp_get_current_user' )
			->once()
			->with()
			->andReturn( $user );

		$this->theme->shouldReceive( 'get_date' )
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

		$message = "Ping: A Theme was just switched! \nID: 1 \nTitle: Diva \nUser: john@doe.com \nDate: 08:57:13, 01-07-2024";

		\WP_Mock::expectFilter(
			'ping_me_on_slack_theme_message',
			$message,
			$theme,
		);

		$expected = $this->theme->get_message( 'A Theme was just switched!' );

		$this->assertSame( $expected, $message );
		$this->assertConditionsMet();
	}
}
