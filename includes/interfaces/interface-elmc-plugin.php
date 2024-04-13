<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Plugin_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Plugin_Interface {

	/**
	 * ELMC_Plugin constructor
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 */
	public function __construct( string $file, string $product_id, array $args = array() );

	/**
	 * Sets the product type
	 *
	 * @return void
	 */
	public function set_type(): void;

	/**
	 * Sets the product meta
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	public function set_product_meta( string $file ): void;
}
