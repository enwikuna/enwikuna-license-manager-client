<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Interface;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\ELMC_ELM_Gateway;

/**
 * Class ELMC_Admin
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin extends ELMC_Singleton_Abstract implements ELMC_Admin_Interface {

	/**
	 * ELMC_Admin constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->init_hooks();
			$this->init_classes();
		}
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_filter( 'plugins_api', array( $this, 'filter_plugins_api' ), 150, 3 );
		add_action( 'elmc_admin_page_action_elmc', array( $this, 'elmc_admin_page_action_elmc_action' ) );
	}

	/**
	 * Init important classes
	 */
	private function init_classes(): void {
		ELMC_Admin_Menus::get_instance();
		ELMC_Admin_Assets::get_instance();
		ELMC_Admin_AJAX::get_instance();
		ELMC_Admin_Notices::get_instance();
		ELMC_Admin_Page_Actions::get_instance();
		ELMC_Admin_Dashboard::get_instance();
	}

	/**
	 * Dashboard actions
	 */
	public function elmc_admin_page_action_elmc_action(): void {
		$products = ELMC::get_instance()->get_products( false );

		if ( ! empty( $products ) ) {
			if ( isset( $_POST['products_to_register'] ) && elmc_is_action( 'register' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$products_to_register = elmc_sanitize_array_data( $_POST['products_to_register'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash

				$this->handle_register_action( $products, $products_to_register );
			}

			if ( elmc_is_action( 'unregister' ) ) {
				$this->handle_unregister_action( $products );
			}
		}
	}

	/**
	 * Pass plugins to API for update and changes
	 *
	 * @param false|object|array $result
	 * @param string $action
	 * @param object $args
	 *
	 * @return false|object|array
	 */
	public function filter_plugins_api( $result, string $action, object $args ) {
		$products       = ELMC::get_instance()->get_products();
		$result_product = false;

		if ( ! isset( $args->slug ) || empty( $products ) ) {
			return $result;
		}

		foreach ( $products as $product ) {
			if ( $args->slug === $product->get_slug() ) {
				$result_product = $product;
				break;
			}
		}

		if ( ! $result_product ) {
			return $result;
		}

		$result = array(
			'name'           => $result_product->get_name(),
			'slug'           => $result_product->get_slug(),
			'author'         => '<a href="' . $result_product->get_author_uri() . '">' . $result_product->get_author() . '</a>',
			'author_profile' => $result_product->get_author_uri(),
			'version'        => $result_product->get_version(),
			'homepage'       => $result_product->get_uri(),
			'sections'       => array(
				'description' => $result_product->get_description(),
				'changelog'   => '',
			),
		);

		$update_info = ELMC_ELM_Gateway::get_instance()->update_info( $result_product );

		if ( ! empty( $update_info ) ) {
			$result = array_replace_recursive( $result, json_decode( wp_json_encode( $update_info ), true ) );

			if ( version_compare( $result['version'], $update_info->new_version, '<' ) ) {
				$result['version'] = $update_info->new_version;
			}
		}

		return (object) $result;
	}

	/**
	 * Get all screen ids where Enwikuna License Manager Client assets should be loaded
	 *
	 * @return array
	 */
	public function get_elmc_admin_screen_ids(): array {
		$elmc_screen_id = 'elmc';
		$screen_ids     = array(
			'index_page_' . $elmc_screen_id . '-network',
			'dashboard_page_' . $elmc_screen_id,
		);

		/**
		 * This filter filters the admin screen IDS
		 *
		 * @param array $screen_ids Includes all screen IDs
		 * @param string $elmc_screen_id Default plugin screen ID
		 *
		 * @wp-hook elmc_admin_screen_ids
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_admin_screen_ids', $screen_ids, $elmc_screen_id );
	}

	/**
	 * Get all external screen ids where Enwikuna License Manager Client assets should be loaded
	 *
	 * @return array
	 */
	public function get_elmc_admin_external_screen_ids(): array {
		$additional_screen_ids = array(
			'plugins',
		);

		/**
		 * This filter filters the admin additional screen IDS
		 *
		 * @param array $additional_screen_ids Includes all additional screen IDs
		 *
		 * @wp-hook elmc_admin_additional_screen_ids
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_admin_additional_screen_ids', $additional_screen_ids );
	}

	/**
	 * Unregister a product
	 *
	 * @param ELMC_Product_Abstract[] $products
	 */
	private function handle_unregister_action( array $products ): void {
		check_admin_referer( 'elmc-unregister', 'elmc-unregister-nonce' );

		$file = elmc_clean( $_GET['file'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		if ( ! empty( $file ) && isset( $products[ $file ] ) ) {
			$license_deactivated = ELMC_ELM_Gateway::get_instance()->deactivate_license( $products[ $file ] );

			if ( $license_deactivated ) {
				$notice = sprintf( wp_kses_post( __( 'Your <strong>%1$s</strong> license was successfully deactivated.', 'enwikuna-license-manager-client' ) ), $products[ $file ]->get_name() );

				ELMC_Admin_Notices::get_instance()->success( $notice );
			} else {
				$notice = sprintf( wp_kses_post( __( 'An error occurred while deactivating your <strong>%1$s</strong> license.', 'enwikuna-license-manager-client' ) ), $products[ $file ]->get_name() );

				ELMC_Admin_Notices::get_instance()->error( $notice );
			}

			$products[ $file ]->flush_cache();
		}

		wp_safe_redirect( admin_url( 'index.php?page=elmc' ) );
		exit;
	}

	/**
	 * Bulk register products
	 *
	 * @param ELMC_Product_Abstract[] $products
	 * @param array $products_to_register
	 */
	private function handle_register_action( array $products, array $products_to_register ): void {
		check_admin_referer( 'elmc-register', 'elmc-register-nonce' );

		if ( ! empty( $products_to_register ) && count( $products_to_register ) > 0 ) {
			$total_products_to_register = count( $products_to_register );
			$activated_licenses         = 0;

			foreach ( $products_to_register as $file => $license_key ) {
				$license_key = elmc_clean( trim( $license_key ) ); // DO NOT UNSLASH
				$file        = elmc_clean( $file ); // DO NOT UNSLASH

				if ( empty( $license_key ) || $products[ $file ]->is_registered() ) {
					continue;
				}

				$license_activated = ELMC_ELM_Gateway::get_instance()->activate_license( $products[ $file ], $license_key );

				if ( $license_activated ) {
					$activated_licenses ++;
				}

				$products[ $file ]->flush_cache();
			}

			$notice = sprintf( _n( '<strong>%1$s</strong> from <strong>%2$s license</strong> activated.', '<strong>%1$s</strong> from <strong>%2$s licenses</strong> activated.', $total_products_to_register, 'enwikuna-license-manager-client' ), $activated_licenses, $total_products_to_register );

			if ( 0 === $activated_licenses ) {
				ELMC_Admin_Notices::get_instance()->error( $notice );
			} elseif ( $activated_licenses > 0 && $activated_licenses < $total_products_to_register ) {
				ELMC_Admin_Notices::get_instance()->warning( $notice );
			} else {
				ELMC_Admin_Notices::get_instance()->success( $notice );
			}
		} else {
			/** @noinspection ForgottenDebugOutputInspection */
			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'enwikuna-license-manager-client' ) );
		}
	}
}
