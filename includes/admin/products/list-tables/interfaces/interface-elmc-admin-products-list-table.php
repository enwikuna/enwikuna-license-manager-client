<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Products\List_Tables\Interfaces;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;

/**
 * Interface ELMC_Admin_Products_List_Table_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Products\List_Tables\Interfaces
 */
interface ELMC_Admin_Products_List_Table_Interface {

	/**
	 * ELMC_Admin_Products_List_Table constructor
	 */
	public function __construct();

	/**
	 * Renders the table list, we override the original class to render the table inside a form
	 * and to render any needed HTML (like the search box). By doing so the callee of a function can simple
	 * forget about any extra HTML
	 */
	public function display_table(): void;

	/**
	 * Prepares the list table items and arguments
	 */
	public function prepare_items(): void;

	/**
	 * Returns the columns for the products list table
	 *
	 * @return array
	 */
	public function get_columns(): array;

	/**
	 * Returns an array of CSS class names for the table
	 *
	 * @return array
	 */
	public function get_table_classes(): array;

	/**
	 * Prepare and outputs content for the name column
	 *
	 * @param ELMC_Product_Abstract $product
	 */
	public function column_name( ELMC_Product_Abstract $product ): void;

	/**
	 * Prepare and outputs content for the version column
	 *
	 * @param ELMC_Product_Abstract $product
	 */
	public function column_version( ELMC_Product_Abstract $product ): void;

	/**
	 * Prepare and outputs content for the expires column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_expires( ELMC_Product_Abstract $product ): string;

	/**
	 * Prepare and outputs content for the note column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_note( ELMC_Product_Abstract $product ): string;

	/**
	 * Prepare and outputs content for the setup wizard column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_setup_wizard( ELMC_Product_Abstract $product ): string;

	/**
	 * Prepare and outputs content for the license column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_license( ELMC_Product_Abstract $product ): string;

	/**
	 * Outputs a message when there are no items to show in the table
	 */
	public function no_items(): void;
}
