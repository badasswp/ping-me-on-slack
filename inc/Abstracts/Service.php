<?php
/**
 * Service Abstraction.
 *
 * This defines the Service abstraction for
 * use by Plugin services.
 *
 * @package PingMeOnSlack
 */

namespace PingMeOnSlack\Abstracts;

use PingMeOnSlack\Core\Client;
use PingMeOnSlack\Interfaces\Kernel;
use PingMeOnSlack\Interfaces\Dispatcher;

abstract class Service implements Kernel {
	/**
	 * Plugin Services.
	 *
	 * @var static[]
	 */
	public static $services = [];

	/**
	 * Get Instance.
	 *
	 * This method gets a single Instance for each
	 * Plugin service.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public static function get_instance() {
		$service = get_called_class();

		if ( ! isset( static::$services[ $service ] ) ) {
			static::$services[ $service ] = new static();
		}

		return static::$services[ $service ];
	}

	/**
	 * Get Date.
	 *
	 * Utility function to obtain the current
	 * date to be logged.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_date(): string {
		return gmdate( 'H:i:s, d-m-Y' );
	}

	/**
	 * Get Dispatcher.
	 *
	 * @since 1.1.3
	 *
	 * @param Dispatcher $dispatcher
	 * @return void
	 */
	protected function get_dispatcher( Dispatcher $dispatcher ): Dispatcher {
		/**
		 * Filter Dispatcher.
		 *
		 * Customise the Dispatch client here, you can
		 * make this extensible.
		 *
		 * @since 1.1.4
		 * @return Dispatcher Client.
		 */
		return apply_filters( 'ping_me_on_slack_dispatcher', $dispatcher );
	}

	/**
	 * Get Client.
	 *
	 * @since 1.1.3
	 *
	 * @return Dispatcher
	 */
	public function get_client(): Dispatcher {
		return $this->get_dispatcher( new Client() );
	}

	/**
	 * Register Service.
	 *
	 * This method registers the Services' logic
	 * for plugin use.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function register(): void;
}
