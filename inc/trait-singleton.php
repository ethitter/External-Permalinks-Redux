<?php
/**
 * Singleton trait.
 *
 * @package External_Permalinks_Redux
 */

trait External_Permalinks_Redux_Singleton {
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
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->_setup();
		}

		return self::$instance;
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
