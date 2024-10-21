<?php

namespace PingMeOnSlack\Tests\Abstracts;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Abstracts\Service;

/**
 * @covers \PingMeOnSlack\Abstracts\Service::__construct
 * @covers \PingMeOnSlack\Abstracts\Service::get_instance
 * @covers \PingMeOnSlack\Core\Client::__construct
 */
class ServiceTest extends TestCase {
	public Service $service;

	public function setUp(): void {
		\WP_Mock::setUp();

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'channel'  => '#general',
					'username' => 'Bryan',
				]
			);

		$this->service = new ConcreteService();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_instance_returns_singleton() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'channel'  => '#general',
					'username' => 'Bryan',
				]
			);

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

	public function test_client_returns_client_instance() {
		$this->assertInstanceOf( Client::class, $this->service->client );
		$this->assertConditionsMet();
	}
}

class ConcreteService extends Service {
	public function register(): void {
		echo 'Register Service...';
	}
}
