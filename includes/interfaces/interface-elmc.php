<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use stdClass;

/**
 * Interface ELMC_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Interface {

	/**
	 * ELMC constructor
	 */
	public function __construct();

	/**
	 * Core plugins loaded action
	 */
	public function core_plugins_loaded_action(): void;

	/**
	 * Default plugins loaded action
	 */
	public function plugins_loaded_action(): void;

	/**
	 * Init products
	 *
	 * @return void
	 */
	public function init_action(): void;

	/**
	 * Register the products
	 */
	public function register_products(): void;

	/**
	 * Adds a new product
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 *
	 * @return bool
	 */
	public function add_product( string $file, string $product_id, array $args = array() ): bool;

	/**
	 * Returns all products
	 *
	 * @param bool $show_free
	 *
	 * @return ELMC_Product_Abstract[]
	 */
	public function get_products( bool $show_free = true ): array;

	/**
	 * Returns a product (plugin) by its basename
	 *
	 * @param $file
	 *
	 * @return false|ELMC_Product_Abstract
	 */
	public function get_product( $file );

	/**
	 * Get registered products
	 *
	 * @return array
	 */
	public function get_registered_products(): array;

	/**
	 * Update registered products
	 *
	 * @param $products
	 *
	 * @return bool
	 */
	public function update_registered_products( $products ): bool;

	/**
	 * Update all registered products data from licensing server
	 *
	 * @param bool $force_flush_cache
	 */
	public function update_registered_products_data( bool $force_flush_cache = false ): void;

	/**
	 * Generates an updatable product in the right format
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 *
	 * @return stdClass
	 */
	public function generate_updatable_product( string $file, string $product_id, array $args = array() ): stdClass;

	/**
	 * Flush product gateway cache
	 */
	public function flush_product_cache_action(): void;

	/**
	 * Add trusted signing key to trusted keys
	 *
	 * @param array $trusted_keys
	 *
	 * @return array
	 */
	public function filter_wp_trusted_keys( array $trusted_keys ): array;

	/**
	 * Add signature host to signature hostnames
	 *
	 * @param array $hostnames
	 *
	 * @return array
	 */
	public function filter_wp_signature_hosts( array $hostnames ): array;

	/**
	 * Replace download URL with signature URL for signature download
	 *
	 * @param false|string $signature_url
	 * @param string $url
	 *
	 * @return false|string
	 */
	public function filter_wp_signature_url( $signature_url, string $url );

	/**
	 * Disable SSL verification for locally created SSL certificates
	 *
	 * @param array $parsed_args
	 *
	 * @return array
	 */
	public function filter_http_request_args( array $parsed_args ): array;
}
