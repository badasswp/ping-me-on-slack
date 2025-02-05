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
 * @covers \PingMeOnSlack\Abstracts\Service::get_instance
 * @covers \PingMeOnSlack\Services\Access::register
 * @covers \PingMeOnSlack\Services\Admin::register
 * @covers \PingMeOnSlack\Services\Boot::register
 * @covers \PingMeOnSlack\Services\Comment::register
 * @covers \PingMeOnSlack\Services\Post::register
 * @covers \PingMeOnSlack\Services\Theme::register
 * @covers \PingMeOnSlack\Services\User::register
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

	public function test_register() {
		$container = new Container();

		/**
		 * We create instances of services so we can
		 * have a populated version of the Service abstraction's instances.
		 */
		foreach ( Container::$services as $service ) {
			$service::get_instance();
		}

		\WP_Mock::expectActionAdded(
			'init',
			[
				Service::$services[ Boot::class ],
				'ping_me_on_slack_translation',
			]
		);

		\WP_Mock::expectActionAdded(
			'wp_login',
			[
				Service::$services[ Access::class ],
				'ping_on_user_login',
			],
			10,
			2
		);

		\WP_Mock::expectActionAdded(
			'wp_logout',
			[
				Service::$services[ Access::class ],
				'ping_on_user_logout',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_init',
			[
				Service::$services[ Admin::class ],
				'register_options_init',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_menu',
			[
				Service::$services[ Admin::class ],
				'register_options_menu',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			[
				Service::$services[ Admin::class ],
				'register_options_styles',
			]
		);

		\WP_Mock::expectActionAdded(
			'transition_comment_status',
			[
				Service::$services[ Comment::class ],
				'ping_on_comment_status_change',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'transition_post_status',
			[
				Service::$services[ Post::class ],
				'ping_on_post_status_change',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'switch_theme',
			[
				Service::$services[ Theme::class ],
				'ping_on_theme_change',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'user_register',
			[
				Service::$services[ User::class ],
				'ping_on_user_creation',
			],
			10,
			2
		);

		\WP_Mock::expectActionAdded(
			'wp_update_user',
			[
				Service::$services[ User::class ],
				'ping_on_user_modification',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'deleted_user',
			[
				Service::$services[ User::class ],
				'ping_on_user_deletion',
			],
			10,
			3
		);

		$container->register();

		$this->assertConditionsMet();
	}
}
