<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Menus_Interface;

/**
 * Class ELMC_Admin_Menus
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_Menus extends ELMC_Singleton_Abstract implements ELMC_Admin_Menus_Interface {

	/**
	 * ELMC_Admin_Menus constructor
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
		add_action( 'admin_menu', array( $this, 'admin_menu_action' ) );
	}

	/**
	 * Introduce Enwikuna License Manager Client admin menu
	 */
	public function admin_menu_action(): void {
		$plugin_base_slug = 'elmc';

		add_dashboard_page(
			Constants::get_constant( 'ELMC_COMPANY_NAME' ),
			Constants::get_constant( 'ELMC_COMPANY_NAME' ),
			'administrator',
			$plugin_base_slug,
			array(
				$this,
				'dashboard_page',
			)
		);
	}

	/**
	 * Output the dashboard page
	 */
	public function dashboard_page(): void {
		ELMC_Admin_Dashboard::get_instance()->output();
	}
}
