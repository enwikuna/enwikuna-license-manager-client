<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Abstracts\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Singleton_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Abstracts\Interfaces
 */
interface ELMC_Singleton_Interface {

	/**
	 * @return $this
	 */
	public static function get_instance(): self;

	/**
	 * Cloning is forbidden
	 */
	public function __clone();

	/**
	 * Unserializing instances of this class is forbidden
	 */
	public function __wakeup();
}
