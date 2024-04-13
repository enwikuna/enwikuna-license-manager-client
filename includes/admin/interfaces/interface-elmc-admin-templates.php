<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Templates_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Templates_Interface {

	/**
	 * ELMC_Admin_Templates constructor
	 */
	public function __construct();

	/**
	 * Get an admin page
	 *
	 * @param string $page
	 * @param array $args
	 */
	public function get_admin_page( string $page, array $args = array() ): void;
}
