<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Uninstall_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Uninstall_Interface {

	/**
	 * Deactivate Enwikuna License Manager Client
	 */
	public static function deactivate(): void;

	/**
	 * Uninstall Enwikuna License Manager Client
	 */
	public static function uninstall(): void;

	/**
	 * Clears all scheduled hooks
	 */
	public static function clear_scheduled_hooks(): void;

	/**
	 * Remove all admin notices
	 *
	 * @return void
	 */
	public static function remove_admin_notices(): void;
}
