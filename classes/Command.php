<?php

namespace Eugene\API;

use WP_CLI;
use WP_CLI_Command;
use Eugene\API\Route;

/**
 * WP CLI commands for API.
 */
class Command extends WP_CLI_Command {

	/**
	 * Force refresh data (override the 1 time per hour request)
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp eugene-api force_refresh
	*/
	public function force_refresh( $args, $assoc_args ) {
		$route = new Route();

		if ( false === $route->get_request_limit() || false !== $route->remove_request_limit() ) {
			WP_CLI::success( __( 'Force refresh has been enabled. Next AJAX call will fetch fresh data.', 'eugene-api' ) );
		} else {
			WP_CLI::error( __( 'No cached data found.', 'eugene-api' ) );
		}
	}

	/**
	 * Disable force refresh and revert request limit to 1 per hour.
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp eugene-api force_refresh_disable
	*/
	public function force_refresh_disable( $args, $assoc_args ) {
		$route = new Route();

		if ( false !== $route->get_request_limit() || false !== $route->set_request_limit() ) {
			WP_CLI::success( __( 'Force refresh has been disabled. Request has been limited to 1 per hour.', 'eugene-api' ) );
		} else {
			WP_CLI::error( __( 'Unable to reset limit.', 'eugene-api' ) );
		}
	}
}
