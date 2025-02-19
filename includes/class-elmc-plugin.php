<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use ELMC_Product_Type_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Plugin_Interface;

/**
 * Class ELMC_Plugin
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Plugin extends ELMC_Product_Abstract implements ELMC_Plugin_Interface {

	/**
	 * ELMC_Plugin constructor
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 */
	public function __construct( string $file, string $product_id, array $args = array() ) {
		$this->set_type();
		$this->set_product_meta( $file );

		parent::__construct( $file, $product_id, $args );
	}

	/**
	 * Sets the product type
	 *
	 * @return void
	 */
	public function set_type(): void {
		$this->type = ELMC_Product_Type_Abstract::PLUGIN;
	}

	/**
	 * Sets the product meta
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	public function set_product_meta( string $file ): void {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( function_exists( 'get_plugin_data' ) && function_exists( '_get_plugin_data_markup_translate' ) ) {
			$this->product_meta = get_plugin_data( WP_PLUGIN_DIR . '/' . $file, false, false );
		}
	}

	/**
	 * Returns the product value
	 *
	 * @param string $meta_key
	 *
	 * @return string
	 */
	protected function get_meta_value( string $meta_key ): string {
		if ( 'URI' === $meta_key ) {
			$meta_key = 'Plugin' . $meta_key;
		}

		$translated_product_meta = _get_plugin_data_markup_translate( $this->file, $this->product_meta );

		return $translated_product_meta[ $meta_key ] ?? '';
	}
}
