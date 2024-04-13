<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Theme_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Theme_Interface {

	/**
	 * ELMC_Theme constructor
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 */
	public function __construct( string $file, string $product_id, array $args = array() );

	/**
	 * Returns the theme key
	 *
	 * @return string
	 */
	public function get_theme_key(): string;
}
