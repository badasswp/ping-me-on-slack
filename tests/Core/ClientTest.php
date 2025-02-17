<?php

namespace PingMeOnSlack\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use Maknz\Slack\Client as SlackClient;

/**
 * @covers \PingMeOnSlack\Core\Client::ping
 * @covers \PingMeOnSlack\Core\Client::get_slack_client
 * @covers pmos_get_settings
 */
class ClientTest extends TestCase {
	public Client $client;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->client = new Client();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_slack_client() {
		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->times( 3 )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'channel'  => '#general',
					'username' => 'Bryan',
					'webhook'  => 'https://slack.com/services',
				]
			);

		$this->assertInstanceOf( SlackClient::class, $client->get_slack_client() );
		$this->assertConditionsMet();
	}

	public function test_ping() {
		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$slack_client = Mockery::mock( SlackClient::class )->makePartial();
		$slack_client->shouldAllowMockingProtectedMethods();

		$client->shouldReceive( 'get_slack_client' )
			->andReturn( $slack_client );

		$slack_client->shouldReceive( 'send' )
			->andReturn( null );

		$client->ping( 'Ping: A post was just published!' );

		$this->assertConditionsMet();
	}

	public function test_ping_throws_exception() {
		$exception = new \Exception( 'No Text Found.' );

		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$slack_client = Mockery::mock( SlackClient::class )->makePartial();
		$slack_client->shouldAllowMockingProtectedMethods();

		$client->shouldReceive( 'get_slack_client' )
			->andReturn( $slack_client );

		$slack_client->shouldReceive( 'send' )
			->with( 'Ping: A post was just published!' )
			->andThrow( $exception );

		\WP_Mock::expectAction( 'ping_me_on_slack_on_ping_error', 'No Text Found.' );

		$client->ping( 'Ping: A post was just published!' );

		$this->assertConditionsMet();
	}
}
