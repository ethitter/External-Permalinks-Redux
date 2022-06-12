<?php
/**
 * Class AdminCallbacks
 *
 * @package External_Permalinks_Redux
 */

/**
 * Test admin callbacks
 */
class AdminCallbacks extends WP_UnitTestCase {
	/**
	 * Redirect destination.
	 */
	const DESTINATION = 'https://w.org/';

	/**
	 * Redirect type.
	 */
	const TYPE = 302;

	/**
	 * Test post ID.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Plugin instance.
	 *
	 * @var external_permalinks_redux
	 */
	protected $plugin;

	/**
	 * Metabox nonce.
	 *
	 * @var string
	 */
	protected $nonce;

	/**
	 * Create some objects with redirects.
	 */
	public function set_up() {
		parent::set_up();

		$this->plugin = external_permalinks_redux::get_instance();

		$this->post_id = $this->factory->post->create(
			array(
				'post_type' => 'post',
			)
		);

		$this->nonce = wp_create_nonce( 'external-permalinks-redux' );
	}

	/**
	 * Test metabox rendering.
	 */
	public function test_meta_box() {
		ob_start();
		$this->plugin->meta_box( get_post( $this->post_id ) );
		$meta_box_contents = ob_get_clean();

		$this->assertStringContainsString( 'value="' . $this->nonce . '"', $meta_box_contents );

		foreach ( array_keys( $this->plugin->status_codes ) as $code ) {
			$this->assertStringContainsString( 'value="' . $code . '"', $meta_box_contents );
		}
	}

	/**
	 * Test metabox save.
	 */
	public function test_save_callback() {
		add_filter( 'use_block_editor_for_post', '__return_false' );

		$_POST[ $this->plugin->meta_key_target . '_nonce' ] = $this->nonce;
		$_POST[ $this->plugin->meta_key_target . '_url' ] = self::DESTINATION;
		$_POST[ $this->plugin->meta_key_target . '_type' ] = self::TYPE;

		$this->plugin->action_save_post( $this->post_id );

		$this->assertStringContainsString( self::DESTINATION, get_post_meta( $this->post_id, $this->plugin->meta_key_target, true ) );
		$this->assertStringContainsString( (string) self::TYPE, get_post_meta( $this->post_id, $this->plugin->meta_key_type, true ) );
	}
}
