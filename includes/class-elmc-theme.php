<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use ELMC_Product_Type_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Theme_Interface;

/**
 * Class ELMC_Theme
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Theme extends ELMC_Product_Abstract implements ELMC_Theme_Interface {

	/**
	 * ELMC_Theme constructor
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
	 * Returns the theme key
	 *
	 * @return string
	 */
	public function get_theme_key(): string {
		return strstr( $this->get_file(), '/', true );
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
			$meta_key = 'Theme' . $meta_key;
		}

		return $this->product_meta->get( $meta_key ) ?? '';
	}

	/**
	 * Sets the product type
	 *
	 * @return void
	 */
	private function set_type(): void {
		$this->type = ELMC_Product_Type_Abstract::THEME;
	}

	/**
	 * Sets the product meta
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	private function set_product_meta( string $file ): void {
		if ( function_exists( 'wp_get_theme' ) ) {
			$this->product_meta = wp_get_theme( strstr( $file, '/', true ) );
		}
	}
}
