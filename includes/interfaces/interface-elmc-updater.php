<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;

/**
 * Interface ELMC_Updater_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Updater_Interface {

	/**
	 * ELMC_Updater constructor
	 *
	 * @param ELMC_Product_Abstract $product
	 */
	public function __construct( ELMC_Product_Abstract $product );

	/**
	 * Check for product updates
	 *
	 * @param object $transient
	 *
	 * @return object
	 */
	public function filter_pre_set_site_transient_update_themes_plugins( object $transient ): object;

	/**
	 * Output upgrade message for a plugin
	 *
	 * @param array $data
	 * @param object $response
	 *
	 * @return void
	 */
	public function in_plugin_updater_message_action( array $data, object $response ): void;
}
