<?php
/**
 * Options Class.
 *
 * This class is responsible for holding the Admin
 * page options.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Admin;

class Options {
	/**
	 * The Form.
	 *
	 * This array defines every single aspect of the
	 * Form displayed on the Admin options page.
	 *
	 * @since 1.1.0
	 */
	public static array $form;

	/**
	 * Define custom static method for calling
	 * dynamic methods for e.g. Options::get_page_title().
	 *
	 * @since 1.1.0
	 *
	 * @param string  $method Method name.
	 * @param mixed[] $args   Method args.
	 *
	 * @return string|mixed[]
	 */
	public static function __callStatic( $method, $args ) {
		static::init();

		$keys = substr( $method, strpos( $method, '_' ) + 1 );
		$keys = explode( '_', $keys );

		$value = '';

		foreach ( $keys as $key ) {
			$value = empty( $value ) ? ( static::$form[ $key ] ?? '' ) : ( $value[ $key ] ?? '' );
		}

		return $value;
	}

	/**
	 * Set up Form.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public static function init(): void {
		static::$form = [
			'page'   => static::get_form_page(),
			'notice' => static::get_form_notice(),
			'fields' => static::get_form_fields(),
			'submit' => static::get_form_submit(),
		];
	}

	/**
	 * Form Page.
	 *
	 * The Form page items containg the Page title,
	 * summary, slug and option name.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_page(): array {
		return [
			'title'   => esc_html__(
				'Ping Me On Slack',
				'ping-me-on-slack'
			),
			'summary' => esc_html__(
				'Get notifications on Slack when changes are made on your WP website.',
				'ping-me-on-slack'
			),
			'slug'    => 'ping-me-on-slack',
			'option'  => 'ping_me_on_slack',
		];
	}

	/**
	 * Form Submit.
	 *
	 * The Form submit items containing the heading,
	 * button name & label and nonce params.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_submit(): array {
		return [
			'heading' => esc_html__( 'Actions', 'ping-me-on-slack' ),
			'button'  => [
				'name'  => 'ping_me_on_slack_save_settings',
				'label' => esc_html__( 'Save Changes', 'ping-me-on-slack' ),
			],
			'nonce'   => [
				'name'   => 'ping_me_on_slack_settings_nonce',
				'action' => 'ping_me_on_slack_settings_action',
			],
		];
	}

	/**
	 * Form Fields.
	 *
	 * The Form field items containing the heading for
	 * each group block and controls.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_fields() {
		return [
			'slack_options'   => [
				'heading'  => esc_html__( 'Slack Options', 'ping-me-on-slack' ),
				'controls' => [
					'username' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Slack Username', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'e.g. John Doe', 'ping-me-on-slack' ),
					],
					'channel'  => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Slack Channel', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'e.g. #general', 'ping-me-on-slack' ),
					],
					'webhook'  => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Slack Webhook', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'e.g. https://hooks.slack.com/services/xxxxxx', 'ping-me-on-slack' ),
					],
				],
			],
			'post_options'    => [
				'heading'  => esc_html__( 'Post Options', 'ping-me-on-slack' ),
				'controls' => [
					'enable_post'  => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Enable Slack', 'ping-me-on-slack' ),
						'summary' => esc_html__( 'Enable Slack messages for Post actions.', 'ping-me-on-slack' ),
					],
					'post_draft'   => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Draft Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a post is saved as draft.', 'ping-me-on-slack' ),
					],
					'post_publish' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Publish Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a post is published.', 'ping-me-on-slack' ),
					],
					'post_trash'   => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Trash Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a post is trashed.', 'ping-me-on-slack' ),
					],
				],
			],
			'comment_options' => [
				'heading'  => esc_html__( 'Comment Options', 'ping-me-on-slack' ),
				'controls' => [
					'enable_comment'  => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Enable Slack', 'ping-me-on-slack' ),
						'summary' => esc_html__( 'Enable Slack messages for Comment actions.', 'ping-me-on-slack' ),
					],
					'comment_approve' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Approval Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a comment is approved.', 'ping-me-on-slack' ),
					],
					'comment_trash'   => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Trash Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a comment is trashed.', 'ping-me-on-slack' ),
					],
				],
			],
			'access_options'  => [
				'heading'  => esc_html__( 'Access Options', 'ping-me-on-slack' ),
				'controls' => [
					'enable_access' => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Enable Slack', 'ping-me-on-slack' ),
						'summary' => esc_html__( 'Enable Slack messages for Access actions.', 'ping-me-on-slack' ),
					],
					'access_login'  => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Login Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a user has logged in.', 'ping-me-on-slack' ),
					],
					'access_logout' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Logout Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a user has logged out.', 'ping-me-on-slack' ),
					],
				],
			],
			'user_options'    => [
				'heading'  => esc_html__( 'User Options', 'ping-me-on-slack' ),
				'controls' => [
					'enable_user' => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Enable Slack', 'ping-me-on-slack' ),
						'summary' => esc_html__( 'Enable Slack messages for User actions.', 'ping-me-on-slack' ),
					],
					'user_create' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Create Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a user is created.', 'ping-me-on-slack' ),
					],
					'user_modify' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Modify Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a user is modified.', 'ping-me-on-slack' ),
					],
					'user_delete' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr( '' ),
						'label'       => esc_html__( 'Delete Message', 'ping-me-on-slack' ),
						'summary'     => esc_html__( 'Message sent when a user is deleted.', 'ping-me-on-slack' ),
					],
				],
			],
		];
	}

	/**
	 * Form Notice.
	 *
	 * The Form notice containing the notice
	 * text displayed on save.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_notice() {
		return [
			'label' => esc_html__( 'Settings Saved.', 'ping-me-on-slack' ),
		];
	}
}
