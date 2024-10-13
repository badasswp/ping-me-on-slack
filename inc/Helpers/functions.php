<?php
/**
 * Functions.
 *
 * This class holds reusable utility functions that can be
 * accessed across the plugin.
 *
 * @package PingMeOnSlack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Plugin Options.
 *
 * @since 1.1.0
 *
 * @param string $option   Plugin option to be retrieved.
 * @param string $fallback Default return value.
 *
 * @return mixed
 */
function pmos_get_settings( $option, $fallback = '' ) {
	return get_option( 'ping_me_on_slack', [] )[ $option ] ?? $fallback;
}
