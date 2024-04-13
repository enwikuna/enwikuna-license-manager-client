<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Dashboard_Interface;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Products\List_Tables\ELMC_Admin_Products_List_Table;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC;

/**
 * Class ELMC_Admin_Dashboard
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_Dashboard extends ELMC_Singleton_Abstract implements ELMC_Admin_Dashboard_Interface {

	/**
	 * ELMC_Admin_Dashboard constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->init_hooks();
		}
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_action( 'load-dashboard_page_elmc', array( $this, 'load_dashboard_page_elmc_action' ) );
	}

	/**
	 * Output the dashboard page
	 */
	public function output(): void {
		ELMC_Admin_Templates::get_instance()->get_admin_page( 'dashboard' );
	}

	/**
	 * Init new products table and assign to global
	 *
	 * @return void
	 */
	public function load_dashboard_page_elmc_action(): void {
		global $elmc_admin_products_list_table;

		ELMC::get_instance()->update_registered_products_data();

		$elmc_admin_products_list_table = new ELMC_Admin_Products_List_Table();
		$elmc_admin_products_list_table->prepare_items();
	}
}
