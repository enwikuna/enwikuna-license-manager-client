<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Assets_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Assets_Interface {

	/**
	 * ELMC_Admin_Assets constructor
	 */
	public function __construct();

	/**
	 * Register admin scripts
	 */
	public function admin_enqueue_scripts_action(): void;

	/**
	 * Register admin styles
	 */
	public function admin_enqueue_styles_action(): void;
}
