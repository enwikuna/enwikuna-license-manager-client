<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_AJAX_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_AJAX_Interface {

	/**
	 * ELMC_Admin_AJAX constructor
	 */
	public function __construct();

	/**
	 * Hides the products expiring notice until it gets re-enabled
	 */
	public function wp_ajax_elmc_dismiss_products_expiring_notice_action(): void;
}
