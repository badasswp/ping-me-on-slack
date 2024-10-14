<?php

namespace PingMeOnSlack\Tests\Helpers;

use WP_Mock\Tools\TestCase;

require_once __DIR__ . '/../../inc/Helpers/functions.php';

/**
 * @covers pmos_get_settings
 */
class FunctionsTest extends TestCase {
	public function test_pmos_get_settings() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'ping_me_on_slack', [] )
			->andReturn(
				[
					'enable_post' => true,
				]
			);

		$is_post_notifications_enabled = pmos_get_settings( 'enable_post', '' );

		$this->assertTrue( $is_post_notifications_enabled );
	}
}
