<?php

namespace PingMeOnSlack\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;

use PingMeOnSlack\Services\Boot;
use PingMeOnSlack\Services\Post;
use PingMeOnSlack\Services\User;
use PingMeOnSlack\Services\Admin;
use PingMeOnSlack\Services\Theme;
use PingMeOnSlack\Services\Access;
use PingMeOnSlack\Services\Comment;
use PingMeOnSlack\Abstracts\Service;

use PingMeOnSlack\Core\Container;

/**
 * @covers \PingMeOnSlack\Core\Container::__construct
 * @covers \PingMeOnSlack\Core\Container::register
 */
class ContainerTest extends TestCase {
	public Container $container;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->container = new Container();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_container_has_list_of_services() {
		$this->assertTrue( in_array( Access::class, Container::$services, true ) );
		$this->assertTrue( in_array( Admin::class, Container::$services, true ) );
		$this->assertTrue( in_array( Boot::class, Container::$services, true ) );
		$this->assertTrue( in_array( Comment::class, Container::$services, true ) );
		$this->assertTrue( in_array( Post::class, Container::$services, true ) );
		$this->assertTrue( in_array( Theme::class, Container::$services, true ) );
		$this->assertTrue( in_array( User::class, Container::$services, true ) );
		$this->assertConditionsMet();
	}
}
