<?php

namespace PingMeOnSlack\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMeOnSlack\Admin\Options;

/**
 * @covers \PingMeOnSlack\Admin\Options::get_form_page
 * @covers \PingMeOnSlack\Admin\Options::get_form_submit
 * @covers \PingMeOnSlack\Admin\Options::get_form_notice
 * @covers \PingMeOnSlack\Admin\Options::get_form_fields
 */
class OptionsTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_form_page() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 2,
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		$form_page = Options::get_form_page();

		$this->assertSame(
			$form_page,
			[
				'title'   => 'Ping Me On Slack',
				'summary' => 'Get notifications on Slack when changes are made on your WP website.',
				'slug'    => 'ping-me-on-slack',
				'option'  => 'ping_me_on_slack',
			]
		);
	}

	public function test_get_form_submit() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 2,
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		$form_submit = Options::get_form_submit();

		$this->assertSame(
			$form_submit,
			[
				'heading' => 'Actions',
				'button'  => [
					'name'  => 'ping_me_on_slack_save_settings',
					'label' => 'Save Changes',
				],
				'nonce'   => [
					'name'   => 'ping_me_on_slack_settings_nonce',
					'action' => 'ping_me_on_slack_settings_action',
				],
			]
		);
	}

	public function test_get_form_notice() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 1,
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		$form_notice = Options::get_form_notice();

		$this->assertSame(
			$form_notice,
			[
				'label' => 'Settings Saved.',
			]
		);
	}
}
