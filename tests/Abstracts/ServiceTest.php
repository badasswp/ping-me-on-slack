<?php

namespace PingMeOnSlack\Tests\Abstracts;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Abstracts\Service;
use PingMeOnSlack\Interfaces\Dispatcher;

/**
 * @covers \PingMeOnSlack\Abstracts\Service::get_instance
 * @covers \PingMeOnSlack\Abstracts\Service::get_date
 * @covers \PingMeOnSlack\Abstracts\Service::get_dispatcher
 * @covers \PingMeOnSlack\Abstracts\Service::get_client
 * @covers \PingMeOnSlack\Abstracts\Service::register
 * @covers \PingMeOnSlack\Core\Client::__construct
 * @covers pmos_get_settings
 */
class ServiceTest extends TestCase {
	public Service $service;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->service = new ConcreteService();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_instance_returns_singleton() {
		$expected_1 = ConcreteService::get_instance();
		$expected_2 = ConcreteService::get_instance();

		$this->assertSame( $expected_1, $expected_2 );
		$this->assertConditionsMet();
	}

	public function test_get_date_returns_gmdate() {
		$expected = $this->service->get_date();

		$this->assertSame( $expected, gmdate( 'H:i:s, d-m-Y' ) );
		$this->assertConditionsMet();
	}

	public function test_register_method_registers_service() {
		$expected = $this->service->register();

		$this->expectOutputString( 'Register Service...' );
		$this->assertConditionsMet();
	}

	public function test_get_client_returns_client_instance() {
		\WP_Mock::userFunction( 'wp_parse_args' )
			->andReturnUsing(
				function ( $arg1, $arg2 ) {
					return array_merge( $arg2, $arg1 );
				}
			);

		\WP_Mock::userFunction( 'get_option' )
			->times( 2 )
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'channel'  => '#general',
					'username' => 'Bryan',
					'webhook'  => 'https://slack.com/services',
				]
			);

		$service = Mockery::mock( ConcreteService::class )->makePartial();
		$service->shouldAllowMockingProtectedMethods();

		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		$service->shouldReceive( 'get_dispatcher' )
			->andReturn( $client );

		$this->assertInstanceOf( Client::class, $service->get_client() );
		$this->assertConditionsMet();
	}

	public function test_get_dispatcher_returns_dispatcher() {
		$service = Mockery::mock( ConcreteService::class )->makePartial();
		$service->shouldAllowMockingProtectedMethods();

		$client = Mockery::mock( Client::class )->makePartial();
		$client->shouldAllowMockingProtectedMethods();

		\WP_Mock::expectFilter( 'ping_me_on_slack_dispatcher', $client );

		$this->assertInstanceOf( Dispatcher::class, $service->get_dispatcher( $client ) );
		$this->assertConditionsMet();
	}
}

class ConcreteService extends Service {
	public function register(): void {
		echo 'Register Service...';
	}
}
