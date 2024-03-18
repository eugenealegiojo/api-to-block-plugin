<?php

namespace Eugene\API;

use WP_REST_Server;
use WP_REST_Response;

/**
 * Handles REST API request.
 */
class Route {

	/**
	 * Route namespace
	 *
	 * @var string
	 */
	public $route_namespace = 'eugene-api/v1';

	/**
	 * Base endpoing
	 *
	 * @var string
	 */
	public $endpoint = '/challenge';

	/**
	 * Cache key for request.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $cache_key = 'eugene_api_cache';

	/**
	 * Limit key.
	 *
	 * @var string
	 */
	private $request_limit_key = 'eugene_api_request_limit';

	/**
	 * This is the source of the API data.
	 *
	 * @var string
	 */
	private $data_source = 'https://miusage.com/v1/challenge/1/';

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->init_hooks();
	}

	/**
	 * Initialize WP hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register rest route.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->route_namespace,
			$this->endpoint, [
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_request_data' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Fetch data from cached or from new request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_request_data() {

		// Check cached data first and make sure it has limit.
		if ( 1 === (int) $this->get_request_limit() ) {
			$cached_request = $this->get_cached_request();
			if ( is_array( $cached_request ) && count( $cached_request ) > 0 ) {
				// Bail and return the cached version
				return new WP_REST_Response( $cached_request );
			}
		}

		// Request new data and cache it.
		$data = $this->fetch_data();
		$this->set_cache_request( $data );

		return new WP_REST_Response( $data );
	}

	/**
	 * Get the cached data.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_cached_request() {
		return get_transient( $this->cache_key );
	}

	/**
	 * Cache the data response.
	 * By default, limit 1 request per hour using expiry HOUR_IN_SECONDS
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed $data
	 * @param  int $expiration
	 *
	 * @return void
	 */
	public function set_cache_request( $data, $expiration = HOUR_IN_SECONDS ) {
		return set_transient( $this->cache_key, $data, $expiration );
	}

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 */
	public function set_request_limit( $limit = 1 ) {
		return set_transient( $this->request_limit_key, (int) $limit );
	}

	/**
	 * Get limit.
	 */
	public function get_request_limit() {
		return get_transient( $this->request_limit_key );
	}

	/**
	 * Remove limit.
	 */
	public function remove_request_limit() {
		return delete_transient( $this->request_limit_key );
	}

	/**
	 * Fetch new data from miusage.com.
	 *
	 * @return mixed
	 */
	protected function fetch_data() {
		$response = wp_remote_get( $this->data_source );
		$code     = wp_remote_retrieve_response_code( $response );
		$body     = wp_remote_retrieve_body( $response );

		if ( 200 !== $code || 'error' === $body || is_wp_error( $body ) ) {
			return [];
		}

		$body = $this->format_data( json_decode( $body, true ) );
		return $body;
	}

	/**
	 * Format response data before using.
	 *
	 * @param mixed
	 */
	public function format_data( $response ) {
		// Bail out early if data doesn't exist.
		if ( ! $response || ! isset( $response['data'] ) ) {
			return $response;
		}

		$headers = [];

		// Headers
		if ( isset( $response['data']['headers'] ) ) {
			foreach ( $response['data']['headers'] as $label ) {
				$key = '';
				if ( 'ID' === $label ) {
					$key = 'id';
				} elseif ( 'First Name' === $label ) {
					$key = 'fname';
				} elseif ( 'Last Name' === $label ) {
					$key = 'lname';
				} elseif ( 'Email' === $label ) {
					$key = 'email';
				} elseif ( 'Date' === $label ) {
					$key = 'date';
				}
				$headers[ $key ] = $label;
			}

			if ( count( $headers ) > 0 ) {
				$response['data']['headers'] = $headers;
			}
		}

		return $response;
	}
}
