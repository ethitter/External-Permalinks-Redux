<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.2
 * @package External_Permalinks_Redux
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function external_permalinks_redux_cgb_block_assets() { // phpcs:ignore
	// Register block editor script for backend.
	wp_register_script(
		'external_permalinks_redux-cgb-block-js',
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
		apply_filters( 'external_permalinks_redux_use-mtime', true ) ? filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ) : null,
		true
	);

	// Register block editor styles for backend.
	wp_register_style(
		'external_permalinks_redux-cgb-block-editor-css',
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
		array( 'wp-edit-blocks' ),
		apply_filters( 'external_permalinks_redux_use-mtime', true ) ? filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) : null
	);

	wp_localize_script(
		'external_permalinks_redux-cgb-block-js',
		'cgbGlobal',
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
		]
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'cgb/block-external-permalinks-redux', array(
			'editor_script' => 'external_permalinks_redux-cgb-block-js',
			'editor_style'  => 'external_permalinks_redux-cgb-block-editor-css',
		)
	);
}

// Hook: Block assets.
add_action( 'init', 'external_permalinks_redux_cgb_block_assets' );
