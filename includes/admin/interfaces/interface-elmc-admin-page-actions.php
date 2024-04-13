<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Page_Actions_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Page_Actions_Interface {

	/**
	 * ELMC_Admin_Page_Actions constructor
	 */
	public function __construct();

	/**
	 * Handle admin pages load and process actions
	 */
	public function admin_init_action(): void;
}
