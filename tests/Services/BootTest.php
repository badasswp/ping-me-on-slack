<?php

namespace PingMySlack\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMySlack\Core\Client;
use PingMySlack\Services\Boot;

/**
 * @covers \PingMySlack\Services\Boot::__construct
 * @covers \PingMySlack\Services\Boot::register
 */
class BootTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->client = Mockery::mock( Client::class )->makePartial();
		$this->client->shouldAllowMockingProtectedMethods();

		$this->boot = Mockery::mock( Boot::class )->makePartial();
		$this->boot->shouldAllowMockingProtectedMethods();
		$this->boot->client = $this->client;
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'init', [ $this->boot, 'ping_me_on_slack_translation' ] );

		$this->boot->register();

		$this->assertConditionsMet();
	}

	public function test_translation_setup() {
		$boot = new \ReflectionClass( Boot::class );

		\WP_Mock::userFunction( 'plugin_basename' )
			->once()
			->with( $boot->getFileName() )
			->andReturn( '/inc/Services/Boot.php' );

		\WP_Mock::userFunction( 'load_plugin_textdomain' )
			->once()
			->with(
				'ping-my-slack',
				false,
				'/inc/Services/../../languages'
			);

		$this->boot->ping_me_on_slack_translation();

		$this->assertConditionsMet();
	}
}
