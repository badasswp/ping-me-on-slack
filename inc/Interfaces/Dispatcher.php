<?php
/**
 * Dispatch Interface.
 *
 * Define the base methods that must be shared
 * across client classes.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Interfaces;

interface Dispatcher {
	/**
	 * Send Message.
	 *
	 * @since 1.1.3
	 *
	 * @return void
	 */
	public function ping( $message ): void;
}
