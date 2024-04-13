<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Notices_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Notices_Interface {

	/**
	 * ELMC_Admin_Notices constructor
	 */
	public function __construct();

	/**
	 * Handles adding of default plugin notices
	 */
	public function wp_loaded_action(): void;

	/**
	 * Adds an admin info notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function info( string $notice, array $args = array() ): void;

	/**
	 * Adds an admin success notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function success( string $notice, array $args = array() ): void;

	/**
	 * Adds an admin warning notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function warning( string $notice, array $args = array() ): void;

	/**
	 * Adds an admin error notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function error( string $notice, array $args = array() ): void;

	/**
	 * Adds a specific notice by counter
	 *
	 * @param string $notice
	 * @param int $successful_count
	 * @param int $total_count
	 * @param array $args
	 */
	public function counter( string $notice, int $successful_count, int $total_count, array $args = array() ): void;

	/**
	 * Output admin notices
	 */
	public function admin_notices_action(): void;

	/**
	 * Removes all admin notices
	 */
	public function remove_all_admin_notices(): void;
}
