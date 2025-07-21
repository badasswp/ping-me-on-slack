<?php

namespace PingMeOnSlack\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use Maknz\Slack\Client as SlackClient;

/**
 * @covers \PingMeOnSlack\Core\Client::ping
 * @covers \PingMeOnSlack\Core\Client::get_client
 * @covers pmos_get_settings
 */
class ClientTest extends TestCase {
	public Client $client;

	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_constructor_should_set_args() {
		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'channel'  => '#general',
					'username' => 'Bryan',
					'webhook'  => 'https://slack.com/services',
				]
			);

		$client = new Client();

		$this->assertSame(
			$client->args,
			[
				'channel'  => '#general',
				'username' => 'Bryan',
			]
		);
		$this->assertConditionsMet();
	}

	public function test_get_client() {
		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$client->args = [
			'channel'  => '#general',
			'username' => 'Bryan',
		];

		\WP_Mock::userFunction( 'get_option' )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'channel'  => '#general',
					'username' => 'Bryan',
					'webhook'  => 'https://slack.com/services',
				]
			);

		\WP_Mock::userFunction( 'wp_parse_args' )
			->andReturnUsing(
				function ( $arg1, $arg2 ) {
					return array_merge( $arg2, $arg1 );
				}
			);

		$this->assertInstanceOf( SlackClient::class, $client->get_client() );
		$this->assertConditionsMet();
	}

	public function test_ping() {
		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$slack_client = Mockery::mock( SlackClient::class )->makePartial();
		$slack_client->shouldAllowMockingProtectedMethods();

		$slack_client->shouldReceive( 'send' )
			->andReturn( null );

		$client->shouldReceive( 'get_client' )
			->andReturn( $slack_client );

		$client->ping( 'Ping: A post was just published!' );

		$this->assertConditionsMet();
	}

	public function test_ping_throws_exception() {
		$exception = new \Exception( 'No Text Found.' );

		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$slack_client = Mockery::mock( SlackClient::class )->makePartial();
		$slack_client->shouldAllowMockingProtectedMethods();

		$slack_client->shouldReceive( 'send' )
			->with( 'Ping: A post was just published!' )
			->andThrow( $exception );

		$client->shouldReceive( 'get_client' )
			->andReturn( $slack_client );

		\WP_Mock::expectAction( 'ping_me_on_slack_on_ping_error', 'No Text Found.' );

		$client->ping( 'Ping: A post was just published!' );

		$this->assertConditionsMet();
	}
}
