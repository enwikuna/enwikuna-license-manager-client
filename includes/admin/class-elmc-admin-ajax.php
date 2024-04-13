<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_AJAX_Interface;

/**
 * Class ELMC_Admin_AJAX
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_AJAX extends ELMC_Singleton_Abstract implements ELMC_Admin_AJAX_Interface {

	/**
	 * ELMC_Admin_AJAX constructor
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
		add_action( 'wp_ajax_elmc_dismiss_products_expiring_notice', array( $this, 'wp_ajax_elmc_dismiss_products_expiring_notice_action' ) );
	}

	/**
	 * Hides the products expiring notice until it gets re-enabled
	 */
	public function wp_ajax_elmc_dismiss_products_expiring_notice_action(): void {
		check_ajax_referer( 'elmc-dismiss-products-expiring-notice', 'security' );

		elmc_update_option( 'dismiss_products_expiring_notice', true );
	}
}
