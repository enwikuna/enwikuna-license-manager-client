<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Install_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Install_Interface {

	/**
	 * ELMC_Install constructor
	 */
	public function __construct();

	/**
	 * Install Enwikuna License Manager Client
	 */
	public function install(): void;

	/**
	 * Check for plugin update -> install again to verify data
	 */
	public function admin_init_action(): void;
}
