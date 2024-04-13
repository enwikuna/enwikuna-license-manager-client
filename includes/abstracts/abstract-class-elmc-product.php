<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Abstracts;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\Interfaces\ELMC_Product_Interface;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC_Updater;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\ELMC_ELM_Gateway;
use WP_Theme;

/**
 * Class ELMC_Product_Abstract
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Abstracts
 */
abstract class ELMC_Product_Abstract implements ELMC_Product_Interface {

	/**
	 * The type of the product
	 *
	 * @var string
	 */
	protected string $type;

	/**
	 * Product file
	 *
	 * @var string
	 */
	protected string $file;

	/**
	 * The id of a product
	 *
	 * @var string
	 */
	private string $id;

	/**
	 * The name / slug of the product
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * If the product is free or paid
	 *
	 * @var bool
	 */
	private bool $free;

	/**
	 * Product meta data
	 *
	 * @var WP_Theme|array
	 */
	protected $product_meta;

	/**
	 * The expiration date - comes from license
	 *
	 * @var string
	 */
	private string $expires = '';

	/**
	 * The status - comes from license
	 *
	 * @var string
	 */
	private string $status = '';

	/**
	 * The license key of the product
	 *
	 * @var string
	 */
	private string $license_key = '';

	/**
	 * The host where the product was installed on
	 *
	 * @var string
	 */
	private string $host = '';

	/**
	 * The setup wizard of the product
	 *
	 * @var string|null
	 */
	private ?string $setup_wizard;

	/**
	 * The updater for each product
	 *
	 * @var ELMC_Updater
	 */
	protected ELMC_Updater $updater;

	/**
	 * ELMC_Product_Abstract constructor
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 */
	public function __construct( string $file, string $product_id, array $args = array() ) {
		$this->file         = $file;
		$this->id           = $product_id;
		$this->free         = $args['free'];
		$this->setup_wizard = $args['setup_wizard'];
		$this->slug         = sanitize_title( $this->get_name() );

		$registered_products = ELMC::get_instance()->get_registered_products();

		if ( isset( $registered_products[ $this->file ] ) ) {
			$this->license_key = $registered_products[ $this->file ]['license_key'] ?? '';
			$this->host        = $registered_products[ $this->file ]['host'] ?? '';
			$this->expires     = $registered_products[ $this->file ]['expires'] ?? '';
			$this->status      = $registered_products[ $this->file ]['status'] ?? '';
		}
	}

	/**
	 * Returns the type of the product
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Returns the ID of the product
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Returns the product file
	 *
	 * @return string
	 */
	public function get_file(): string {
		return $this->file;
	}

	/**
	 * Returns the product slug
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Returns the product name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->get_meta_value( 'Name' );
	}

	/**
	 * Returns the product author
	 *
	 * @return string
	 */
	public function get_author(): string {
		return $this->get_meta_value( 'Author' );
	}

	/**
	 * Returns the author URI
	 *
	 * @return string
	 */
	public function get_author_uri(): string {
		return $this->get_meta_value( 'AuthorURI' );
	}

	/**
	 * Returns the product version
	 *
	 * @return string
	 */
	public function get_version(): string {
		return $this->get_meta_value( 'Version' );
	}

	/**
	 * Returns the product description
	 */
	public function get_description(): string {
		return $this->get_meta_value( 'Description' );
	}

	/**
	 * Returns the product URI
	 *
	 * @return string
	 */
	public function get_uri(): string {
		return $this->get_meta_value( 'URI' );
	}

	/**
	 * Returns the license key of the product
	 *
	 * @return string
	 */
	public function get_license_key(): string {
		if ( ! $this->is_registered() ) {
			return false;
		}

		if ( $this->is_free() ) {
			return '';
		}

		return $this->license_key;
	}

	/**
	 * Returns the host where the product was activated on
	 *
	 * @return string
	 */
	public function get_host(): string {
		return $this->host;
	}

	/**
	 * Returns the setup wizard of the product
	 *
	 * @return string|null
	 */
	public function get_setup_wizard(): ?string {
		return $this->setup_wizard;
	}

	/**
	 * Checks if the product is a free product e.g. the Enwikuna License Manager Client
	 *
	 * @return bool|mixed
	 */
	public function is_free() {
		return $this->free;
	}

	/**
	 * Return the URL the product page in case the license expired
	 *
	 * @return string
	 */
	public function get_renewal_url(): string {
		/**
		 * This filter filters the renewal URL of a product
		 *
		 * @param string $renewal_url The renewal URL
		 * @param ELMC_Product_Abstract $product The product object
		 *
		 * @wp-hook elmc_{product_slug}_renewal_url
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_' . $this->get_slug() . '_renewal_url', trailingslashit( $this->get_author_uri() ) . $this->get_slug(), $this );
	}

	/**
	 * Set the updater for a product
	 *
	 * @param ELMC_Updater $updater
	 *
	 * @return void
	 */
	public function set_updater( ELMC_Updater $updater ): void {
		$this->updater = $updater;
	}

	/**
	 * Checks if the product is registered or not
	 *
	 * @return bool
	 */
	public function is_registered(): bool {
		return ! empty( $this->license_key ) || $this->is_free();
	}

	/**
	 * Refresh the expiration date
	 *
	 * @return bool|string
	 */
	public function refresh_expiration_date() {
		$license_info = ELMC_ELM_Gateway::get_instance()->license_info( $this );

		if ( ! empty( $license_info ) && ! is_wp_error( $license_info ) ) {
			$this->set_expiration_date( ! empty( $license_info->expires_at ) ? $license_info->expires_at : '' );

			return $license_info->expires_at;
		}

		return false;
	}

	/**
	 * Returns the expiration date
	 *
	 * @param string $format
	 *
	 * @return false|string
	 */
	public function get_expiration_date( string $format = 'd.m.Y' ) {
		if ( empty( $this->expires ) || ! $this->is_registered() ) {
			return false;
		}

		return gmdate( $format, strtotime( $this->expires ) );
	}

	/**
	 * Checks if the product is expired
	 *
	 * @return bool
	 */
	public function has_expired(): bool {
		if ( empty( $this->expires ) || ! $this->is_registered() ) {
			return false;
		}

		return strtotime( $this->expires ) < time();
	}

	/**
	 * Updates the expiration date of a product
	 *
	 * @param string $expires
	 */
	public function set_expiration_date( string $expires ): void {
		$registered_products = ELMC::get_instance()->get_registered_products();

		if ( isset( $registered_products[ $this->file ] ) ) {
			$registered_products[ $this->file ]['expires'] = $expires;
			$this->expires                                 = $expires;
		}

		ELMC::get_instance()->update_registered_products( $registered_products );
	}

	/**
	 * Refresh the status
	 *
	 * @return bool|string
	 */
	public function refresh_status() {
		$license_info = ELMC_ELM_Gateway::get_instance()->license_info( $this );

		if ( ! empty( $license_info ) && ! is_wp_error( $license_info ) ) {
			$this->set_status( ! empty( $license_info->status ) ? $license_info->status : '' );

			return $license_info->status;
		}

		return false;
	}

	/**
	 * Returns the status
	 *
	 * @return false|string
	 */
	public function get_status() {
		if ( empty( $this->status ) || ! $this->is_registered() ) {
			return false;
		}

		return $this->status;
	}

	/**
	 * Updates the status of a license
	 *
	 * @param string $status
	 *
	 * @return void
	 */
	public function set_status( string $status ): void {
		$registered_products = ELMC::get_instance()->get_registered_products();

		if ( isset( $registered_products[ $this->file ] ) ) {
			$registered_products[ $this->file ]['status'] = $status;
			$this->status                                 = $status;
		}

		ELMC::get_instance()->update_registered_products( $registered_products );
	}

	/**
	 * Registers a new product
	 *
	 * @param string $license_key
	 * @param string $expires
	 * @param string $status
	 */
	public function register( string $license_key, string $expires = '', string $status = '' ): void {
		$registered_products = ELMC::get_instance()->get_registered_products();

		if ( ! isset( $registered_products[ $this->file ] ) ) {
			$registered_products[ $this->file ] = array(
				'license_key' => $license_key,
				'host'        => elmc_prepare_license_host_url( home_url() ),
				'expires'     => $expires,
				'status'      => $status,
			);

			$this->license_key = $license_key;

			if ( ! empty( $expires ) ) {
				$this->expires = $expires;
			}

			if ( ! empty( $status ) ) {
				$this->status = $status;
			}
		}

		ELMC::get_instance()->update_registered_products( $registered_products );
	}

	/**
	 * Unregisters a product
	 */
	public function unregister(): void {
		$registered_products = ELMC::get_instance()->get_registered_products();

		if ( isset( $registered_products[ $this->file ] ) ) {
			unset( $registered_products[ $this->file ] );

			$this->license_key = '';
			$this->host        = '';
			$this->expires     = '';
			$this->status      = '';
		}

		ELMC::get_instance()->update_registered_products( array_filter( $registered_products ) );
	}

	/**
	 * Flush product cache
	 */
	public function flush_cache(): void {
		elmc_delete_transient( "update_info_cache_{$this->id}" );
		elmc_delete_transient( "license_info_cache_{$this->id}" );
	}

	/**
	 * Returns the product value
	 *
	 * @param string $meta_key
	 *
	 * @return string
	 */
	protected function get_meta_value( string $meta_key ): string {
		return $this->product_meta[ $meta_key ] ?? '';
	}
}
