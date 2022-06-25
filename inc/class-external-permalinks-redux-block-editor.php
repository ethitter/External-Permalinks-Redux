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
		add_action( 'rest_api_init', array( $this, 'register_meta' ) );
		add_filter( 'is_protected_meta', array( $this, 'allow_meta_updates' ), 10, 3 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
	}

	/**
	 * Register meta for access in block editor.
	 *
	 * @return void
	 */
	public function register_meta() {
		global $wp_version;

		if (
			! function_exists( 'register_meta' )
			|| version_compare( $wp_version, '4.6.0', '<' )
		) {
			return;
		}

		register_meta(
			'post',
			external_permalinks_redux::get_instance()->meta_key_target,
			array(
				'default'           => '',
				'description'       => __(
					'Redirect destination',
					'external-permalinks-redux'
				),
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
				'show_in_rest'      => true,
				'single'            => true,
			)
		);

		register_meta(
			'post',
			external_permalinks_redux::get_instance()->meta_key_type,
			array(
				'default'           => 0,
				'description'       => __(
					'Redirect status code',
					'external-permalinks-redux'
				),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'show_in_rest'      => true,
				'single'            => true,
			)
		);
	}

	/**
	 * Allow meta updates from REST API.
	 *
	 * @param bool   $protected Whether meta is protected or not.
	 * @param string $meta_key  Meta key.
	 * @param string $meta_type Meta key type.
	 * @return bool
	 */
	public function allow_meta_updates( $protected, $meta_key, $meta_type ) {
		if ( 'post' !== $meta_type ) {
			return $protected;
		}

		if (
			external_permalinks_redux::get_instance()->meta_key_target === $meta_key
			|| external_permalinks_redux::get_instance()->meta_key_type === $meta_key
		) {
			return false;
		}

		return $protected;
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

		wp_localize_script(
			$asset_handle,
			'externalPermalinksReduxConfig',
			array(
				'metaKeys'    => array(
					'target' => external_permalinks_redux::get_instance()->meta_key_target,
					'type'   => external_permalinks_redux::get_instance()->meta_key_type,
				),
				'postTypes'   => external_permalinks_redux::get_instance()->post_types,
				'statusCodes' => $this->_get_status_codes(),
			)
		);

		wp_set_script_translations(
			$asset_handle,
			'external-permalinks-redux',
			dirname( dirname( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Format status codes for use with `SelectControl` component.
	 *
	 * @return array
	 */
	protected function _get_status_codes() {
		$codes = external_permalinks_redux::get_instance()->status_codes;
		$formatted = array(
			array(
				'label'    => __( '-- Select --', 'external-permalinks-redux' ),
				'value'    => 0,
			),
		);

		foreach ( $codes as $code => $label ) {
			$formatted[] = array(
				'label' => $label,
				'value' => $code,
			);
		}

		return $formatted;
	}
}
