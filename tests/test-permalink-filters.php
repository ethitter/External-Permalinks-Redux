<?php
/**
 * Class PermalinkFilters
 *
 * @package External_Permalinks_Redux
 */

/**
 * Test permalink filters
 */
class PermalinkFilters extends WP_UnitTestCase {
	/**
	 * Redirect destination.
	 */
	const DESTINATION = 'https://w.org/';

	/**
	 * Test post ID.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Test page ID.
	 *
	 * @var int
	 */
	protected $page_id;

	/**
	 * Create some objects with redirects.
	 */
	public function set_up() {
		parent::set_up();

		$plugin = external_permalinks_redux::get_instance();

		$this->post_id = $this->factory->post->create(
			array(
				'post_type' => 'post',
			)
		);

		update_post_meta( $this->post_id, $plugin->meta_key_target, self::DESTINATION );

		$this->page_id = $this->factory->post->create(
			array(
				'post_type' => 'page',
			)
		);

		update_post_meta( $this->page_id, $plugin->meta_key_target, self::DESTINATION );
	}

	/**
	 * Test post permalink filter.
	 */
	public function test_post() {
		$this->assertEquals( self::DESTINATION, get_permalink( $this->post_id ) );
	}

	/**
	 * Test page link filter.
	 */
	public function test_page() {
		$this->assertEquals( self::DESTINATION, get_page_link( $this->page_id ) );
	}
}
