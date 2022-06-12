<?php
/**
 * Singleton base class.
 *
 * @package External_Permalinks_Redux
 */

abstract class External_Permalinks_Redux_Singleton {
	/**
	 * Singleton!
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Instantiate class as a singleton.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
			static::$instance->_setup();
		}

		return static::$instance;
	}

	/**
	 * Unused constructor.
	 */
	final private function __construct() {}

	/**
	 * Set up class.
	 *
	 * @return void
	 */
	abstract protected function _setup();
}
