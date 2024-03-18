<?php
/**
 * Class for registering api block
 */

namespace Eugene\API;

class Block {

	public function init() {
		// Regiser block
		register_block_type( EUGENE_API_DIR . '/build' );

		// Frontend
		if ( ! is_admin() ) {
			add_action( 'enqueue_block_assets', [ $this, 'enqueue_scripts' ] );
		}
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style(
			'eugene-api-block-style',
			EUGENE_API_BUILD_URL . '/style-index.css',
			EUGENE_API_VERSION
		);
	}

	/**
	 * Block template.
	 */
	public function render_block_template( $block ) {
		include EUGENE_API_DIR . 'includes/block-render-template.php';
	}
}
