<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Page_Actions_Interface;

/**
 * Class ELMC_Admin_Page_Actions
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_Page_Actions extends ELMC_Singleton_Abstract implements ELMC_Admin_Page_Actions_Interface {

	/**
	 * ELMC_Admin_Page_Actions constructor
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
		add_action( 'admin_init', array( $this, 'admin_init_action' ), 999 );
	}

	/**
	 * Handle admin pages load and process actions
	 */
	public function admin_init_action(): void {
		global $current_tab;

		if ( $this->is_elmc_admin_page() && ! wp_doing_ajax() ) {
			$page = str_replace( 'elmc-', '', elmc_clean( wp_unslash( $_GET['page'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			/**
			 * This filter filters the admin page tabs array
			 *
			 * @param array $page_tabs_array Includes the page tabs array
			 *
			 * @wp-hook elmc_admin_{page}_tabs_array
			 * @since 1.0.0
			 */
			$page_tabs_array = apply_filters( 'elmc_admin_' . $page . '_tabs_array', array() );

			/**
			 * This hook fires before an admin page action
			 *
			 * @wp-hook elmc_admin_before_page_action_{page}
			 * @since 1.0.0
			 */
			do_action( 'elmc_admin_before_page_action_' . $page );

			// Set current tab in case its a page with tabbing
			if ( ! empty( $page_tabs_array ) ) {
				$current_tab = elmc_get_current_tab( array_key_first( $page_tabs_array ) );
			}

			/**
			 * This hook fires the admin page action
			 *
			 * @wp-hook elmc_admin_page_action_{page}
			 * @since 1.0.0
			 */
			do_action( 'elmc_admin_page_action_' . $page );

			/**
			 * This hook fires after an admin page action
			 *
			 * @wp-hook elmc_admin_after_page_action_{page}
			 * @since 1.0.0
			 */
			do_action( 'elmc_admin_after_page_action_' . $page );
		}
	}

	/**
	 * Checks if the current page is a Enwikuna License Manager Client page
	 *
	 * @return bool
	 */
	private function is_elmc_admin_page(): bool {
		$is_elmc_page = false;

		if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page = elmc_clean( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! empty( $page ) && false !== strpos( $page, 'elmc' ) ) {
				$is_elmc_page = true;
			}
		}

		return $is_elmc_page;
	}
}
