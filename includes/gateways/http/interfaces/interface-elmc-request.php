<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP\Interfaces;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP\ELMC_Request;
use stdClass;
use WP_Error;

/**
 * Interface ELMC_Request_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP\Interfaces
 */
interface ELMC_Request_Interface {

	/**
	 * ELMC_Request constructor
	 */
	public function __construct();

	/**
	 * Sends a request to the license server
	 *
	 * @param string $action
	 * @param string $license_key
	 * @param array $args
	 *
	 * @return ELMC_Request
	 */
	public function request( string $action, string $license_key = '', array $args = array() ): ELMC_Request;

	/**
	 * Returns the response of the request
	 *
	 * @param string $type
	 *
	 * @return WP_Error|false|stdClass
	 */
	public function get_response( string $type = 'all' );
}
