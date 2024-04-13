<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Dashboard_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Dashboard_Interface {

	/**
	 * ELMC_Admin_Dashboard constructor
	 */
	public function __construct();

	/**
	 * Output the dashboard page
	 */
	public function output(): void;

	/**
	 * Init new products table and assign to global
	 *
	 * @return void
	 */
	public function load_dashboard_page_elmc_action(): void;
}
