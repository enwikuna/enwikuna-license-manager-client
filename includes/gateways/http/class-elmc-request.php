<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP\Interfaces\ELMC_Request_Interface;
use stdClass;
use WP_Error;

/**
 * Class ELMC_Request
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP
 */
class ELMC_Request implements ELMC_Request_Interface {

	/**
	 * The API URL of the licensing system
	 *
	 * @var string
	 */
	private string $api_url;

	/**
	 * Request headers
	 *
	 * @var array
	 */
	private array $wp_request_headers;

	/**
	 * Response code
	 *
	 * @var int
	 */
	private int $response_code = 500;

	/**
	 * Response array
	 *
	 * @var stdClass|null
	 */
	private ?stdClass $response;

	/**
	 * ELMC_Request constructor
	 */
	public function __construct() {
		$key    = Constants::get_constant( 'ELMC_REST_API_KEY' );
		$secret = Constants::get_constant( 'ELMC_REST_API_SECRET' );

		$this->api_url            = Constants::get_constant( 'ELMC_REST_API_URL' );
		$this->wp_request_headers = array(
			'timeout'       => 45,
			'redirection'   => 5,
			'httpversion'   => '1.0',
			'blocking'      => true,
			'cookies'       => '',
			'sslverify'     => true,
			'Authorization' => 'Basic ' . base64_encode( $key . ':' . $secret ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		);
	}

	/**
	 * Sends a request to the license server
	 *
	 * @param string $action
	 * @param string $license_key
	 * @param array $args
	 *
	 * @return ELMC_Request
	 */
	public function request( string $action, string $license_key = '', array $args = array() ): ELMC_Request {
		$request_url  = $this->api_url;
		$default_args = array( 'headers' => $this->wp_request_headers );

		if ( 'ping' !== $action ) {
			$request_url = $this->api_url . $action . '/' . $license_key;
		}

		$response = wp_safe_remote_request( $request_url, wp_parse_args( $default_args, $args ) );

		if ( ! empty( $response ) && empty( $response->errors ) ) {
			$this->response_code = wp_remote_retrieve_response_code( $response );
			$this->response      = json_decode( wp_remote_retrieve_body( $response ) );
		}

		return $this;
	}

	/**
	 * Returns the response of the request
	 *
	 * @param string $type
	 *
	 * @return WP_Error|false|stdClass
	 */
	public function get_response( string $type = 'all' ) {
		if ( ! empty( $this->response ) ) {
			if ( 'ping' === $type ) {
				return 200 === $this->response_code;
			}

			if ( $this->is_error() ) {
				return new WP_Error( $this->response->code, $this->response->message, $this->response->data );
			}

			if ( 'all' === $type ) {
				return $this->response;
			}

			return $this->response->$type ?? false;
		}

		return false;
	}

	/**
	 * Checks if response is an error
	 *
	 * @return bool
	 */
	private function is_error(): bool {
		if ( in_array( $this->response_code, array( 500, 400, 404, 405, 403, 401 ), true ) ) {
			return true;
		}

		return false;
	}
}
