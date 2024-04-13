<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Gateways;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\HTTP\ELMC_Request;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\Interfaces\ELMC_ELM_Gateway_Interface;

/**
 * Class ELMC_ELM_Gateway
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Gateways
 */
class ELMC_ELM_Gateway extends ELMC_Singleton_Abstract implements ELMC_ELM_Gateway_Interface {

	/**
	 * Enwikuna License Manager request
	 *
	 * @var ELMC_Request
	 */
	private ELMC_Request $elmc_request;

	/**
	 * Default license key of Enwikuna License Manager Client
	 *
	 * @var string
	 */
	private string $elmc_license_key = 'AAAA-AAAA-AAAA-AAAA';

	/**
	 * ELMC_ELM_Gateway constructor
	 */
	public function __construct() {
		$this->elmc_request = new ELMC_Request();
	}

	/**
	 * Checks if the connection to the Enwikuna License Manager is successful and stable
	 *
	 * @return bool
	 */
	public function is_connected(): bool {
		return $this->elmc_request->request( 'ping' )->get_response( 'ping' );
	}

	/**
	 * Activates a product license
	 *
	 * @param ELMC_Product_Abstract $product
	 * @param string $license_key
	 *
	 * @return bool
	 */
	public function activate_license( ELMC_Product_Abstract $product, string $license_key ): bool {
		$args = array(
			'method' => 'POST',
			'body'   => array(
				'host'         => home_url(),
				'product_uuid' => $product->get_id(),
			),
		);

		$response = $this->elmc_request->request( 'licenses/activate', $license_key, $args )->get_response();

		if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
			$expires_at = ! empty( $response->data->expires_at ) ? $response->data->expires_at : '';
			$status     = ! empty( $response->data->status ) ? $response->data->status : '';

			$product->register( $license_key, $expires_at, $status );

			return true;
		}

		return false;
	}

	/**
	 * Deactivates a product license
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return bool
	 */
	public function deactivate_license( ELMC_Product_Abstract $product ): bool {
		$args = array(
			'method' => 'POST',
			'body'   => array(
				'host' => home_url(),
			),
		);

		$response = $this->elmc_request->request( 'licenses/deactivate', $product->get_license_key(), $args )->get_response();

		if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
			$product->unregister();

			return true;
		}

		return false;
	}

	/**
	 * Returns info about the license
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return false|object
	 */
	public function license_info( ELMC_Product_Abstract $product ) {
		if ( ! $product->is_registered() ) {
			return false;
		}

		$license_info_cache = elmc_get_transient( $this->get_license_info_cache_key( $product ) );

		if ( ! empty( $license_info_cache ) ) {
			return $license_info_cache;
		}

		$response = $this->elmc_request->request( 'licenses', $product->get_license_key() )->get_response();

		if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
			$cache_data = $response->data;

			elmc_set_transient( $this->get_license_info_cache_key( $product ), $cache_data, 6 * Constants::get_constant( 'HOUR_IN_SECONDS' ) );

			return $response->data;
		}

		return false;
	}

	/**
	 * Returns the latest update including the version for a product
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return false|object
	 */
	public function update_info( ELMC_Product_Abstract $product ) {
		if ( ! $product->is_registered() ) {
			return false;
		}

		$update_info_cache = elmc_get_transient( $this->get_update_info_cache_key( $product ) );

		if ( ! empty( $update_info_cache ) && ! isset( $_GET['force-check'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $update_info_cache;
		}

		if ( 'enwikuna-license-manager-client' === $product->get_slug() ) {
			$license_key = $this->elmc_license_key;
		} else {
			$license_key = $product->get_license_key();
		}

		$response = $this->elmc_request->request( 'products/update', $license_key )->get_response();

		if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
			$cache_data = $response->data;

			elmc_set_transient( $this->get_update_info_cache_key( $product ), $cache_data, 6 * Constants::get_constant( 'HOUR_IN_SECONDS' ) );

			return $response->data;
		}

		return false;
	}

	/**
	 * Returns update info cache key
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	private function get_update_info_cache_key( ELMC_Product_Abstract $product ): string {
		return "update_info_cache_{$product->get_id()}";
	}

	/**
	 * Returns license info cache key
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	private function get_license_info_cache_key( ELMC_Product_Abstract $product ): string {
		return "license_info_cache_{$product->get_id()}";
	}
}
