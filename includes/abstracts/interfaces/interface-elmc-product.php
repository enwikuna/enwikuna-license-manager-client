<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Abstracts\Interfaces;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\ELMC_Updater;

/**
 * Interface ELMC_Product_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Abstracts\Interfaces
 */
interface ELMC_Product_Interface {

	/**
	 * ELMC_Product_Abstract constructor
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 */
	public function __construct( string $file, string $product_id, array $args = array() );

	/**
	 * Returns the type of the product
	 *
	 * @return string
	 */
	public function get_type(): string;

	/**
	 * Returns the ID of the product
	 *
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Returns the product file
	 *
	 * @return string
	 */
	public function get_file(): string;

	/**
	 * Returns the product slug
	 *
	 * @return string
	 */
	public function get_slug(): string;

	/**
	 * Returns the product name
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Returns the product author
	 *
	 * @return string
	 */
	public function get_author(): string;

	/**
	 * Returns the author URI
	 *
	 * @return string
	 */
	public function get_author_uri(): string;

	/**
	 * Returns the product version
	 *
	 * @return string
	 */
	public function get_version(): string;

	/**
	 * Returns the product description
	 */
	public function get_description(): string;

	/**
	 * Returns the product URI
	 *
	 * @return string
	 */
	public function get_uri(): string;

	/**
	 * Returns the license key of the product
	 *
	 * @return string
	 */
	public function get_license_key(): string;

	/**
	 * Returns the host where the product was activated on
	 *
	 * @return string
	 */
	public function get_host(): string;

	/**
	 * Returns the setup wizard of the product
	 *
	 * @return string|null
	 */
	public function get_setup_wizard(): ?string;

	/**
	 * Checks if the product is a free product e.g. the Enwikuna License Manager Client
	 *
	 * @return bool|mixed
	 */
	public function is_free();

	/**
	 * Return the URL the product page in case the license expired
	 *
	 * @return string
	 */
	public function get_renewal_url(): string;

	/**
	 * Set the updater for a product
	 *
	 * @param ELMC_Updater $updater
	 *
	 * @return void
	 */
	public function set_updater( ELMC_Updater $updater ): void;

	/**
	 * Checks if the product is registered or not
	 *
	 * @return bool
	 */
	public function is_registered(): bool;

	/**
	 * Refresh the expiration date
	 *
	 * @return bool|string
	 */
	public function refresh_expiration_date();

	/**
	 * Returns the expiration date
	 *
	 * @param string $format
	 *
	 * @return false|string
	 */
	public function get_expiration_date( string $format = 'd.m.Y' );

	/**
	 * Checks if the product is expired
	 *
	 * @return bool
	 */
	public function has_expired(): bool;

	/**
	 * Updates the expiration date of a product
	 *
	 * @param string $expires
	 */
	public function set_expiration_date( string $expires ): void;

	/**
	 * Refresh the status
	 *
	 * @return bool|string
	 */
	public function refresh_status();

	/**
	 * Returns the status
	 *
	 * @return false|string
	 */
	public function get_status();

	/**
	 * Updates the status of a license
	 *
	 * @param string $status
	 *
	 * @return void
	 */
	public function set_status( string $status ): void;

	/**
	 * Registers a new product
	 *
	 * @param string $license_key
	 * @param string $expires
	 * @param string $status
	 */
	public function register( string $license_key, string $expires = '', string $status = '' ): void;

	/**
	 * Unregisters a product
	 */
	public function unregister(): void;

	/**
	 * Flush product cache
	 */
	public function flush_cache(): void;
}
