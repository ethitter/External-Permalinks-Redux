<?php
/**
 * Test block-editor integration.
 *
 * @package External_Permalinks_Redux
 */

/**
 * Class TestClassExternalPermalinksReduxBlockEditor.
 *
 * @coversDefaultClass External_Permalinks_Redux_Block_Editor
 */
class TestClassExternalPermalinksReduxBlockEditor extends WP_UnitTestCase {
	/**
	 * Test meta registration.
	 *
	 * @covers ::register_meta()
	 */
	public function test_register_meta() {
		global $wp_meta_keys;

		$wp_meta_keys = null;

		$this->assertFalse(
			registered_meta_key_exists(
				'post',
				external_permalinks_redux::get_instance()->meta_key_target
			),
			'Failed to assert that "target" meta key is not registered.'
		);

		$this->assertFalse(
			registered_meta_key_exists(
				'post',
				external_permalinks_redux::get_instance()->meta_key_type
			),
			'Failed to assert that "type" meta key is not registered.'
		);

		External_Permalinks_Redux_Block_Editor::get_instance()->register_meta();

		$this->assertTrue(
			registered_meta_key_exists(
				'post',
				external_permalinks_redux::get_instance()->meta_key_target
			),
			'Failed to assert that "target" meta key is registered.'
		);

		$this->assertTrue(
			registered_meta_key_exists(
				'post',
				external_permalinks_redux::get_instance()->meta_key_type
			),
			'Failed to assert that "type" meta key is registered.'
		);
	}

	/**
	 * Test overridding private meta editing.
	 *
	 * @covers ::allow_meta_updates()
	 */
	public function test_allow_meta_updates() {
		$this->assertTrue(
			External_Permalinks_Redux_Block_Editor::get_instance()->allow_meta_updates(
				true,
				'_a_random_key',
				'term'
			),
			'Failed to assert that a term\'s key is not modified..'
		);

		$this->assertTrue(
			External_Permalinks_Redux_Block_Editor::get_instance()->allow_meta_updates(
				true,
				'_a_random_key',
				'post'
			),
			'Failed to assert that unrelated key\'s protection is not modified.'
		);

		$this->assertFalse(
			External_Permalinks_Redux_Block_Editor::get_instance()->allow_meta_updates(
				true,
				external_permalinks_redux::get_instance()->meta_key_target,
				'post'
			),
			'Failed to assert that "target" key is not protected.'
		);

		$this->assertFalse(
			External_Permalinks_Redux_Block_Editor::get_instance()->allow_meta_updates(
				true,
				external_permalinks_redux::get_instance()->meta_key_type,
				'post'
			),
			'Failed to assert that "type" key is not protected.'
		);
	}

	/**
	 * Test script enqueueing.
	 *
	 * @covers ::enqueue()
	 */
	public function test_enqueue() {
		$asset_handle = 'external-permalinks-redux';

		$this->assertFalse(
			wp_script_is( $asset_handle, 'enqueued' ),
			'Failed to assert that script is not enqueued.'
		);

		remove_all_actions( 'admin_init' );
		do_action( 'admin_init' );
		external_permalinks_redux::get_instance()->action_admin_init();
		External_Permalinks_Redux_Block_Editor::get_instance()->enqueue();

		$this->assertTrue(
			wp_script_is( $asset_handle, 'enqueued' ),
			'Failed to assert that script is enqueued.'
		);

		$this->assertStringContainsString(
			'externalPermalinksReduxConfig',
			wp_scripts()->get_data( $asset_handle, 'data' ),
			'Failed to assert that configuration data is added.'
		);
	}
}
