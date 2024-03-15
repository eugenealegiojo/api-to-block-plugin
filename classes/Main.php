<?php

namespace Eugene\API;

use WP_CLI;
use Eugene\API\Route;
use Eugene\API\Command;
use Eugene\API\Admin;

final class Main {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		define( 'EUGENE_API_FILE', trailingslashit( dirname( __DIR__, 1 ) ) . 'eugene-api.php' );
		define( 'EUGENE_API_DIR', trailingslashit( plugin_dir_path( EUGENE_API_FILE ) ) );
		define( 'EUGENE_API_URL', trailingslashit( plugin_dir_url( __DIR__ ) ) );
		define( 'EUGENE_API_ASSETS_URL', EUGENE_API_URL . 'assets' );
		define( 'EUGENE_API_BUILD_DIR', EUGENE_API_DIR . 'assets/build' );
		define( 'EUGENE_API_BUILD_URL', EUGENE_API_URL . 'assets/build' );
		define( 'EUGENE_API_VERSION', '1.0.0' );
	}

	/**
	 * Register all the plugin hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		register_activation_hook( EUGENE_API_FILE, [ $this, 'activate' ] );
		register_deactivation_hook( EUGENE_API_FILE, [ $this, 'deactivate' ] );
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize all the plugin logic.
	 */
	public function init() {
		// WP CLI Command
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'eugene-api', Command::class );
		}

		// API Route
		( new Route() )->init();

		// Admin
		if ( is_admin() ) {
			( new Admin() )->init();
		}
	}

	/**
	 * Activation hook.
	 */
	public function activate() {
		( new Route() )->set_request_limit();
	}

	/**
	 * Deactivation hook.
	 */
	public function deactivate() {
		delete_transient( 'eugene_api_request_limit' );
		delete_transient( 'eugene_api_cache' );
	}
}
