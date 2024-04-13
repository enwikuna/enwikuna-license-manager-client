<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Menus_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Menus_Interface {

	/**
	 * ELMC_Admin_Menus constructor
	 */
	public function __construct();

	/**
	 * Introduce Enwikuna License Manager Client admin menu
	 */
	public function admin_menu_action(): void;

	/**
	 * Output the dashboard page
	 */
	public function dashboard_page(): void;
}
