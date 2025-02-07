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

	public function test_get_form_fields() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'return' => function ( $text, $domain = 'ping-me-on-slack' ) {
					return $text;
				},
			]
		);

		$form_fields = Options::get_form_fields();

		$this->assertSame(
			$form_fields,
			[
				'slack_options'   => [
					'heading'  => 'Slack Options',
					'controls' => [
						'username' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Slack Username',
							'summary'     => 'e.g. John Doe',
						],
						'channel'  => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Slack Channel',
							'summary'     => 'e.g. #general',
						],
						'webhook'  => [
							'control'     => 'password',
							'placeholder' => '',
							'label'       => 'Slack Webhook',
							'summary'     => 'e.g. https://hooks.slack.com/services/xxxxxx',
						],
					],
				],
				'post_options'    => [
					'heading'  => 'Post Options',
					'controls' => [
						'enable_post'  => [
							'control' => 'checkbox',
							'label'   => 'Enable Slack',
							'summary' => 'Enable Slack messages for Post actions.',
						],
						'post_draft'   => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Draft Message',
							'summary'     => 'Message sent when a post is saved as draft.',
						],
						'post_publish' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Publish Message',
							'summary'     => 'Message sent when a post is published.',
						],
						'post_trash'   => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Trash Message',
							'summary'     => 'Message sent when a post is trashed.',
						],
					],
				],
				'comment_options' => [
					'heading'  => 'Comment Options',
					'controls' => [
						'enable_comment'  => [
							'control' => 'checkbox',
							'label'   => 'Enable Slack',
							'summary' => 'Enable Slack messages for Comment actions.',
						],
						'comment_approve' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Approval Message',
							'summary'     => 'Message sent when a comment is approved.',
						],
						'comment_trash'   => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Trash Message',
							'summary'     => 'Message sent when a comment is trashed.',
						],
					],
				],
				'access_options'  => [
					'heading'  => 'Access Options',
					'controls' => [
						'enable_access' => [
							'control' => 'checkbox',
							'label'   => 'Enable Slack',
							'summary' => 'Enable Slack messages for Access actions.',
						],
						'access_login'  => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Login Message',
							'summary'     => 'Message sent when a user has logged in.',
						],
						'access_logout' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Logout Message',
							'summary'     => 'Message sent when a user has logged out.',
						],
					],
				],
				'user_options'    => [
					'heading'  => 'User Options',
					'controls' => [
						'enable_user' => [
							'control' => 'checkbox',
							'label'   => 'Enable Slack',
							'summary' => 'Enable Slack messages for User actions.',
						],
						'user_create' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Create Message',
							'summary'     => 'Message sent when a user is created.',
						],
						'user_modify' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Modify Message',
							'summary'     => 'Message sent when a user is modified.',
						],
						'user_delete' => [
							'control'     => 'text',
							'placeholder' => '',
							'label'       => 'Delete Message',
							'summary'     => 'Message sent when a user is deleted.',
						],
					],
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
