<?php
/**
 * Class RedirectCallbacks
 *
 * @package External_Permalinks_Redux
 */

/**
 * Test redirect callbacks
 */
class RedirectCallbacks extends WP_UnitTestCase {
	/**
	 * Redirect destination.
	 */
	const DESTINATION = 'https://w.org/';

	/**
	 * Plugin instance.
	 *
	 * @var external_permalinks_redux
	 */
	protected $plugin;

	/**
	 * Create some objects with redirects.
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = external_permalinks_redux::get_instance();
	}

	/**
	 * Helper to retrieve a clean post.
	 *
	 * @return int
	 */
	protected function get_new_post() {
		return $this->factory->post->create(
			[
				'post_type' => 'post',
			]
		);
	}

	/**
	 * Test post with default redirect code.
	 */
	public function test_post_redirect_default_status() {
		$post_id = $this->get_new_post();
		update_post_meta( $post_id, $this->plugin->meta_key_target, static::DESTINATION );

		$redirect = $this->plugin->get_redirect_data( $post_id );

		$this->assertEquals( static::DESTINATION, $redirect['link'] );
		$this->assertEquals( 302, $redirect['type'] );
	}

	/**
	 * test post with custom redirect code.
	 */
	public function test_post_redirect_custom_status() {
		$post_id = $this->get_new_post();
		update_post_meta( $post_id, $this->plugin->meta_key_target, static::DESTINATION );
		update_post_meta( $post_id, $this->plugin->meta_key_type, 307 );

		$redirect = $this->plugin->get_redirect_data( $post_id );

		$this->assertEquals( static::DESTINATION, $redirect['link'] );
		$this->assertEquals( 307, $redirect['type'] );
	}

	/**
	 * Test post with redirect type but no destination.
	 */
	public function test_post_redirect_missing_destination() {
		$post_id = $this->get_new_post();
		update_post_meta( $post_id, $this->plugin->meta_key_type, 307 );

		$redirect = $this->plugin->get_redirect_data( $post_id );

		$this->assertFalse( $redirect );
	}

	/**
	 * Test post without redirect.
	 */
	public function test_post_no_redirect() {
		$post_id = $this->get_new_post();
		$redirect = $this->plugin->get_redirect_data( $post_id );

		$this->assertFalse( $redirect );
	}
}
