<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Dependencies_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Dependencies_Interface {

	/**
	 * ELMC_Dependencies constructor
	 */
	public function __construct();

	/**
	 * Return if the plugin is loadable or not
	 *
	 * @return bool
	 */
	public function is_loadable(): bool;
}
