<?php

namespace Eugene\API;

use Eugene\API\Route;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Initializes class.
	 */
	function init() {
		$this->hooks();
	}

	/**
	 * Add hooks for the admin area.
	 *
	 */
	public function hooks() {
		// Add menu
		add_action( 'admin_menu', [ $this, 'add_admin_page' ] );

		// Enqueue scripts and styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Admin footer
		add_action( 'admin_footer', [ $this, 'admin_footer' ] );
	}

	/**
	 * Add admin page for the plugin.
	 *
	 */
	public function add_admin_page() {
		add_menu_page(
			esc_html__( 'Eugene API', 'eugene-api' ),
			esc_html__( 'Eugene API', 'eugene-api' ),
			'manage_options',
			'eugene-api',
			[ $this, 'display' ],
			'dashicons-rest-api',
			100
		);
	}

	/**
	 * Admin scripts and js.
	 *
	 */
	public function enqueue_assets( $page ) {
		if ( strpos( $page, 'eugene-api' ) === false ) {
			return;
		}

		$route      = new Route();
		$script_obj = [
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'restUrl'   => rest_url( $route->route_namespace . $route->endpoint ),
			'restNonce' => wp_create_nonce( 'eugene-api-admin' ),
		];

		// General styles and js
		wp_enqueue_style(
			'eugene-api-admin',
			EUGENE_API_ASSETS_URL . '/css/admin.css',
			false,
			EUGENE_API_VERSION
		);

		wp_enqueue_script(
			'eugene-api-admin',
			EUGENE_API_ASSETS_URL . '/js/admin.js',
			[ 'jquery', 'underscore', 'wp-util' ],
			EUGENE_API_VERSION,
			false
		);

		wp_localize_script( 'eugene-api-admin', 'eugene_api_obj', $script_obj );
	}

	/**
	 * Render admin scripts/templates.
	 */
	function admin_footer() {
		include EUGENE_API_DIR . 'includes/admin-js-templates.php';
	}

	/**
	 * Render admin settings page.
	 */
	public function display() {
		include EUGENE_API_DIR . 'includes/admin-page.php';
	}
}
