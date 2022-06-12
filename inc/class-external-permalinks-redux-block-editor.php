<?php
/**
 * Block editor support.
 *
 * @package External_Permalinks_Redux
 */

/**
 * Class Block_Editor.
 */
class External_Permalinks_Redux_Block_Editor {
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
		if ( empty( self::$instance ) ) {
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
	protected function _setup() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue block-editor script.
	 *
	 * @return void
	 */
	public function enqueue() {
		global $pagenow;

		if ( 'widgets.php' === $pagenow ) {
			return;
		}

		$asset_data   = require_once dirname( dirname( __FILE__ ) ) . '/assets/build/index.asset.php';
		$asset_handle = 'external-permalinks-redux';

		wp_enqueue_script(
			$asset_handle,
			plugins_url( 'assets/build/index.js', dirname( __FILE__ ) ),
			$asset_data['dependencies'],
			$asset_data['version'],
			true
		);
	}
}
