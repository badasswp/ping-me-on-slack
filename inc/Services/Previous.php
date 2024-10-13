<?php
/**
 * Admin Class.
 *
 * This class is responsible for setting up the
 * Options fields for the plugin.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Services;

use Carbon_Fields\Field;
use Carbon_Fields\Container;
use Carbon_Fields\Carbon_Fields;

use PingMeOnSlack\Abstracts\Service;
use PingMeOnSlack\Interfaces\Kernel;

class Admin extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'plugins_loaded', [ $this, 'carbon_fields_init' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'get_admin_page' ] );
	}

	/**
	 * Boot Carbon Fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function carbon_fields_init(): void {
		Carbon_Fields::boot();
	}

	/**
	 * Get Admin Page.
	 *
	 * This method loads all the settings option
	 * for the Admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_admin_page(): void {
		Container::make( 'theme_options', 'Ping Me on Slack' )
			->set_page_file( 'ping-me-on-slack' )
			->set_icon( 'dashicons-format-chat' )
			->set_page_menu_position( 3 )
			->add_fields( $this->get_admin_fields() );

		update_option(
			'ping_me_on_slack',
			[
				'webhook'  => carbon_get_theme_option( 'ping_me_on_slack_webhook' ),
				'username' => carbon_get_theme_option( 'ping_me_on_slack_username' ),
				'channel'  => carbon_get_theme_option( 'ping_me_on_slack_channel' ),
			]
		);

		/**
		 * Fire after saving default options.
		 *
		 * This action provides a way to save additional field
		 * options that extend's the plugin features.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $options Plugin options.
		 * @return void
		 */
		do_action( 'ping_me_on_slack_admin_page', get_option( 'ping_me_on_slack', [] ) );
	}

	/**
	 * Get Admin Fields.
	 *
	 * This method grabs all the different fields to
	 * be registered.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	private function get_admin_fields(): array {
		$fields = [
			'summary'  => [
				'type' => 'html',
				'name' => 'ping_me_on_slack_summary',
				'html' => esc_html__( 'Get notifications on Slack when changes are made on your WP website.', 'ping-me-on-slack' ),
			],
			'username' => [
				'type'  => 'text',
				'name'  => 'ping_me_on_slack_username',
				'html'  => esc_html__( 'Slack Username', 'ping-me-on-slack' ),
				'label' => esc_html__( 'John Doe', 'ping-me-on-slack' ),
				'width' => 50,
			],
			'channel'  => [
				'type'  => 'text',
				'name'  => 'ping_me_on_slack_channel',
				'html'  => esc_html__( 'Slack Channel', 'ping-me-on-slack' ),
				'label' => esc_html__( 'e.g. #general', 'ping-me-on-slack' ),
				'width' => 50,
			],
			'webhook'  => [
				'type'  => 'text',
				'name'  => 'ping_me_on_slack_webhook',
				'html'  => esc_html__( 'Slack Webhook', 'ping-me-on-slack' ),
				'label' => esc_html__( 'e.g. https://hooks.slack.com/services/xxxxxx', 'ping-me-on-slack' ),
			],
		];

		/**
		 * Filter the Admin Fields.
		 *
		 * This filter provides a way to admit in new Admin fields
		 * that extend the plugin's features.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $fields Admin Fields.
		 * @return mixed[]
		 */
		$fields = (array) apply_filters( 'ping_me_on_slack_admin_fields', $fields );

		return array_map(
			function ( $field ) {
				$field_type = $field['type'] ?? '';
				$field_name = $field['name'] ?? '';
				$field_html = $field['html'] ?? '';

				if ( 'html' === $field_type ) {
					return Field::make( $field_type, $field_name )
						->set_html( $field['html'] ?? '' );
				}

				if ( 'text' === $field_type ) {
					return Field::make( $field_type, $field_name, $field_html )
						->help_text( $field['label'] ?? '' )
						->set_width( $field['width'] ?? 100 );
				}
			},
			$fields
		);
	}
}
