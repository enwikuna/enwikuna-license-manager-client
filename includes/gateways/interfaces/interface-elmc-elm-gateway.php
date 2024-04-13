<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Gateways\Interfaces;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;

/**
 * Interface ELMC_ELM_Gateway_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Gateways\Interfaces
 */
interface ELMC_ELM_Gateway_Interface {

	/**
	 * ELMC_ELM_Gateway constructor
	 */
	public function __construct();

	/**
	 * Checks if the connection to the Enwikuna License Manager is successful and stable
	 *
	 * @return bool
	 */
	public function is_connected(): bool;

	/**
	 * Activates a product license
	 *
	 * @param ELMC_Product_Abstract $product
	 * @param string $license_key
	 *
	 * @return bool
	 */
	public function activate_license( ELMC_Product_Abstract $product, string $license_key ): bool;

	/**
	 * Deactivates a product license
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return bool
	 */
	public function deactivate_license( ELMC_Product_Abstract $product ): bool;

	/**
	 * Returns info about the license
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return false|object
	 */
	public function license_info( ELMC_Product_Abstract $product );

	/**
	 * Returns the latest update including the version for a product
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return false|object
	 */
	public function update_info( ELMC_Product_Abstract $product );
}
